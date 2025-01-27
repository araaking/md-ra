<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\BiayaSekolah;
use App\Models\TahunAjaran;
use App\Models\BukuTabungan;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class PembayaranController extends Controller
{
    public function show($id)
    {
        $pembayaran = Pembayaran::find($id);
        if (!$pembayaran) {
            return redirect()->route('pembayaran.index')->with('error', 'Pembayaran not found.');
        }
        return view('pembayaran.show', compact('pembayaran'));
    }

    public function index()
    {
        $pembayarans = Pembayaran::with(['siswa', 'tahunAjaran'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('pembayaran.index', compact('pembayarans'));
    }

    public function create(Request $request)
    {
        $tahunAktif = TahunAjaran::where('is_active', true)->firstOrFail();
        $kelasList = Kelas::all();
        $selectedKelasId = $request->input('kelas_id');
        $siswas = [];

        if ($selectedKelasId) {
            $siswas = Siswa::where('class_id', $selectedKelasId)
                ->where('academic_year_id', $tahunAktif->id)
                ->where('status', 'Aktif')
                ->get();
        }

        return view('pembayaran.create', compact(
            'kelasList',
            'tahunAktif',
            'siswas',
            'selectedKelasId'
        ));
    }

    public function store(Request $request)
    {
        $request->validate(Pembayaran::validationRules($request->jenis_biaya));

        DB::beginTransaction();

        try {
            $siswa = Siswa::findOrFail($request->siswa_id);
            $this->validateStudentCategory($siswa, $request->jenis_biaya);

            $biayaSekolah = BiayaSekolah::where('tahun_ajaran_id', $siswa->academic_year_id)
                ->where('jenis_biaya', $request->jenis_biaya)
                ->where('kategori_siswa', $siswa->category)
                ->first();

            if ($biayaSekolah) {
                $maxJumlah = $this->applyDiscount($biayaSekolah->jumlah, $siswa->category, $request->jenis_biaya);
                
                if ($request->jumlah > $maxJumlah) {
                    return back()->with('error', 'Jumlah melebihi ketentuan untuk kategori siswa ini.');
                }
            }

            $pembayaran = Pembayaran::create([
                'siswa_id' => $request->siswa_id,
                'tahun_ajaran_id' => $siswa->academic_year_id,
                'jenis_biaya' => $request->jenis_biaya,
                'bulan_hijri' => $request->bulan_hijri,
                'jumlah' => $request->jumlah,
                'metode_pembayaran' => $request->metode_pembayaran,
                'keterangan' => $request->keterangan,
                'is_processed' => $request->metode_pembayaran === 'cash'
            ]);

            if ($request->metode_pembayaran === 'cicilan') {
                $this->createCicilanTransaction($pembayaran);
            }

            DB::commit();

            return redirect()->route('pembayaran.index')
                ->with('success', 'Pembayaran berhasil disimpan!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function exportPDF($siswa_id)
    {
        $siswa = Siswa::with(['kelas', 'bukuTabungans.transaksis', 'pembayarans'])
            ->findOrFail($siswa_id);

        $tahunAktif = TahunAjaran::where('is_active', true)->first();
        
        // Hitung total tabungan
        $totalTabungan = $siswa->bukuTabungans->sum(function($buku) {
            return $buku->transaksis->where('jenis', 'simpanan')->sum('jumlah');
        });

        // Hitung komponen
        $adminFeePercentage = $this->hitungAdminFee($siswa->category, false);
        $adminFee = $totalTabungan * ($adminFeePercentage / 100);
        
        $potongan = [
            'IKK' => BiayaSekolah::getBiaya('IKK', $siswa->category),
            'SPP' => BiayaSekolah::getBiaya('SPP', $siswa->category) * $this->hitungBulanTertunggak($siswa),
            'Uang_Pangkal' => BiayaSekolah::getBiaya('Uang Pangkal', $siswa->category),
            'THB' => BiayaSekolah::getBiaya('THB', $siswa->category),
            'Foto' => BiayaSekolah::getBiaya('Foto', $siswa->category),
            'Raport' => BiayaSekolah::getBiaya('Raport', $siswa->category),
            'UAM' => BiayaSekolah::getBiaya('UAM', $siswa->category),
            'Tunggakan' => $this->hitungTunggakanTahunLalu($siswa),
            'Pinjaman' => $this->hitungPinjaman($siswa)
        ];

        $totalPotongan = array_sum($potongan);
        $sisaTabungan = ($totalTabungan - $adminFee) - $totalPotongan;

        $pdf = Pdf::loadView('pembayaran.export-pdf', [
            'siswa' => $siswa,
            'tahunAktif' => $tahunAktif,
            'totalTabungan' => $totalTabungan,
            'adminFee' => $adminFee,
            'adminFeePercentage' => $adminFeePercentage,
            'potongan' => $potongan,
            'totalPotongan' => $totalPotongan,
            'sisaTabungan' => $sisaTabungan
        ]);

        return $pdf->download("Laporan-Keuangan-{$siswa->name}.pdf");
    }

    // ============ HELPER METHODS ============
    
    private function validateStudentCategory(Siswa $siswa, $jenisBiaya)
    {
        $invalidCases = [
            'Anak Guru' => ['SPP', 'IKK', 'Uang Pangkal'],
            'Anak Yatim' => ['SPP'],
        ];

        if (array_key_exists($siswa->category, $invalidCases) && 
            in_array($jenisBiaya, $invalidCases[$siswa->category])) {
            abort(422, "Siswa kategori {$siswa->category} tidak diizinkan membayar $jenisBiaya.");
        }
    }

    private function applyDiscount($jumlah, $category, $jenisBiaya)
    {
        if ($category === 'Kakak Beradik' && in_array($jenisBiaya, ['SPP', 'IKK'])) {
            return $jumlah * 0.8; // Diskon 20%
        }
        return $jumlah;
    }

    private function hitungAdminFee($category, $isPenarikanAwal = false)
    {
        if ($category === 'Anak Guru') return 5;
        
        return match($isPenarikanAwal) {
            true => 10,
            false => 8
        };
    }

    private function hitungBulanTertunggak($siswa)
    {
        $startYear = Carbon::parse($siswa->academicYear->year_start);
        $endYear = Carbon::parse($siswa->academicYear->year_end);
        $totalBulan = $startYear->diffInMonths($endYear);
        
        $sudahBayar = $siswa->pembayarans()
            ->where('jenis_biaya', 'SPP')
            ->count();

        return max($totalBulan - $sudahBayar, 0);
    }

    private function hitungTunggakanTahunLalu($siswa)
    {
        return Pembayaran::where('siswa_id', $siswa->id)
            ->where('tahun_ajaran_id', '<>', $siswa->academic_year_id)
            ->where('is_processed', false)
            ->sum('jumlah');
    }

    private function hitungPinjaman($siswa)
    {
        return Transaksi::whereHas('bukuTabungan', function($q) use ($siswa) {
            $q->where('siswa_id', $siswa->id)
              ->where('jenis', 'pinjaman');
        })->sum('jumlah');
    }

    private function createCicilanTransaction(Pembayaran $pembayaran)
    {
        $bukuTabungan = $pembayaran->siswa->bukuTabungans()
            ->where('tahun_ajaran_id', $pembayaran->tahun_ajaran_id)
            ->firstOrFail();

        Transaksi::create([
            'buku_tabungan_id' => $bukuTabungan->id,
            'jenis' => 'cicilan',
            'jumlah' => $pembayaran->jumlah,
            'tanggal' => now(),
            'keterangan' => "Cicilan {$pembayaran->jenis_biaya} - {$pembayaran->keterangan}"
        ]);
    }
}