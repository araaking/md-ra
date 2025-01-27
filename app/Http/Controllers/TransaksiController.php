<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\BukuTabungan;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;

class TransaksiController extends Controller
{
    // Menampilkan daftar semua transaksi
    public function index(Request $request)
        {
            $query = Transaksi::with(['bukuTabungan.siswa.kelas']);
        
            // Filter by specific transaction type if specified
            if ($request->has('jenis') && in_array($request->jenis, ['simpanan', 'cicilan', 'penarikan'])) {
                $query->where('jenis', $request->jenis);
            }
            
            $transaksis = $query->orderBy('tanggal', 'desc')
                ->paginate(10);
        
            return view('transaksi.index', compact('transaksis'));
        }

    // Form transaksi simpanan & cicilan
    public function create(Request $request)
    {
        // Cek tahun ajaran aktif
        $tahunAktif = TahunAjaran::where('is_active', true)->first();

        if (!$tahunAktif) {
            return redirect()->route('dashboard')
                ->with('error', 'Tidak ada tahun ajaran aktif!');
        }

        $kelas = Kelas::all();
        $selectedKelasId = $request->input('kelas_id');
        $bukuTabungans = BukuTabungan::query()
            ->where('tahun_ajaran_id', $tahunAktif->id) // Filter tahun ajaran aktif
            ->whereHas('siswa', function ($query) {
                $query->where('status', 'Aktif'); // Hanya siswa aktif
            });

        // Filter berdasarkan kelas jika dipilih
        if ($selectedKelasId) {
            $bukuTabungans->whereHas('siswa', function ($query) use ($selectedKelasId) {
                $query->where('class_id', $selectedKelasId);
            });
        }

        $bukuTabungans = $bukuTabungans->with('siswa')->get();

        return view('transaksi.create', compact(
            'kelas',
            'bukuTabungans',
            'selectedKelasId',
            'tahunAktif'
        ));
    }

    // Simpan transaksi simpanan/cicilan
    public function store(Request $request)
    {
        $request->validate([
            'buku_tabungan_id' => 'required|exists:buku_tabungan,id',
            'simpanan' => 'nullable|numeric|min:0',
            'cicilan' => 'nullable|numeric|min:0',
            'keterangan' => 'nullable|string|max:255'
        ]);

        // Validasi minimal satu transaksi
        if (empty($request->simpanan) && empty($request->cicilan)) {
            return back()->with('error', 'Harus mengisi simpanan atau cicilan!');
        }

        // Simpan transaksi simpanan
        if ($request->filled('simpanan')) {
            Transaksi::create([
                'buku_tabungan_id' => $request->buku_tabungan_id,
                'jenis'            => 'simpanan',
                'jumlah'           => $request->simpanan,
                'tanggal'          => now(),
                'keterangan'       => $request->keterangan
            ]);
        }

        // Simpan transaksi cicilan
        if ($request->filled('cicilan')) {
            Transaksi::create([
                'buku_tabungan_id' => $request->buku_tabungan_id,
                'jenis'            => 'cicilan',
                'jumlah'           => $request->cicilan,
                'tanggal'          => now(),
                'keterangan'       => $request->keterangan
            ]);
        }

        return redirect()->route('transaksi.index')
            ->with('success', 'Transaksi berhasil disimpan!');
    }

    // Form penarikan
    public function createPenarikan(Request $request)
    {
        // Cek tahun ajaran aktif
        $tahunAktif = TahunAjaran::where('is_active', true)->first();

        if (!$tahunAktif) {
            return redirect()->route('dashboard')
                ->with('error', 'Tidak ada tahun ajaran aktif!');
        }

        $kelas = Kelas::all();
        $selectedKelasId = $request->input('kelas_id');
        $bukuTabungans = BukuTabungan::query()
            ->where('tahun_ajaran_id', $tahunAktif->id) // Filter tahun ajaran aktif
            ->whereHas('siswa', function ($query) {
                $query->where('status', 'Aktif'); // Hanya siswa aktif
            });

        // Filter berdasarkan kelas jika dipilih
        if ($selectedKelasId) {
            $bukuTabungans->whereHas('siswa', function ($query) use ($selectedKelasId) {
                $query->where('class_id', $selectedKelasId);
            });
        }

        $bukuTabungans = $bukuTabungans->with('siswa')->get();

        return view('transaksi.penarikan', compact(
            'kelas',
            'bukuTabungans',
            'selectedKelasId',
            'tahunAktif'
        ));
    }

    // Simpan penarikan
    public function storePenarikan(Request $request)
    {
        $request->validate([
            'buku_tabungan_id'  => 'required|exists:buku_tabungan,id',
            'jumlah'            => 'required|numeric|min:0',
            'sumber_penarikan'  => 'required|in:simpanan,cicilan',
            'keterangan'        => 'nullable|string|max:255'
        ]);

        // Cek saldo sumber penarikan
        $totalSumber = Transaksi::where('buku_tabungan_id', $request->buku_tabungan_id)
            ->where('jenis', $request->sumber_penarikan)
            ->sum('jumlah');

        // Validasi saldo
        if ($totalSumber < $request->jumlah) {
            return back()->with('error', 'Saldo ' . $request->sumber_penarikan . ' tidak mencukupi!');
        }

        // Simpan transaksi penarikan
        Transaksi::create([
            'buku_tabungan_id'  => $request->buku_tabungan_id,
            'jenis'             => 'penarikan',
            'jumlah'            => $request->jumlah,
            'tanggal'           => now(),
            'sumber_penarikan'  => $request->sumber_penarikan,
            'keterangan'        => $request->keterangan
        ]);

        return redirect()->route('transaksi.index')
            ->with('success', 'Penarikan berhasil dicatat!');
    }

    // Hapus transaksi
    public function destroy(Transaksi $transaksi)
    {
        $transaksi->delete();
        return redirect()->route('transaksi.index')
            ->with('success', 'Transaksi berhasil dihapus!');
    }
}