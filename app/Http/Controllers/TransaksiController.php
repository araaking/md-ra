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
    public function index()
    {
        // Get current active tahun ajaran
        $tahunAjaran = TahunAjaran::where('is_active', true)->firstOrFail();
        
        // Get all academic years for the dropdown filter
        $allTahunAjaran = TahunAjaran::orderBy('year_name', 'desc')->get();
        
        // Use selected academic year from query parameter or default to active
        $selectedTahunId = request('tahun_ajaran_id', $tahunAjaran->id);
        $selectedTahun = TahunAjaran::findOrFail($selectedTahunId);

        // Get transactions with pagination
        $transaksis = Transaksi::whereHas('bukuTabungan', function($query) use ($selectedTahun) {
            $query->where('tahun_ajaran_id', $selectedTahun->id);
        })
        ->orderBy('tanggal', 'desc')
        ->paginate(10);
    
        // Calculate totals
        $totalSimpanan = Transaksi::whereHas('bukuTabungan', function($query) use ($selectedTahun) {
            $query->where('tahun_ajaran_id', $selectedTahun->id);
        })
        ->where('jenis', 'simpanan')
        ->sum('jumlah');
    
        $totalCicilan = Transaksi::whereHas('bukuTabungan', function($query) use ($selectedTahun) {
            $query->where('tahun_ajaran_id', $selectedTahun->id);
        })
        ->where('jenis', 'cicilan')
        ->sum('jumlah');
    
        $totalPenarikan = Transaksi::whereHas('bukuTabungan', function($query) use ($selectedTahun) {
            $query->where('tahun_ajaran_id', $selectedTahun->id);
        })
        ->where('jenis', 'penarikan')
        ->sum('jumlah');
    
        return view('transaksi.index', compact(
            'transaksis',
            'tahunAjaran',
            'selectedTahun',
            'totalSimpanan',
            'totalCicilan',
            'totalPenarikan',
            'allTahunAjaran'
        ));
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
        
        // Build query
        $query = BukuTabungan::query()
            ->where('tahun_ajaran_id', $tahunAktif->id)
            ->whereHas('siswa', function ($query) {
                $query->where('status', 'Aktif');
            });

        // Filter by class if selected
        if ($selectedKelasId) {
            $query->whereHas('siswa', function ($query) use ($selectedKelasId) {
                $query->where('class_id', $selectedKelasId);
            });
        }

        // Get data with relationships
        $bukuTabungans = $query->with(['siswa', 'transaksis'])->get()
            ->map(function ($buku) {
                // Calculate total savings and withdrawals
                $buku->totalSimpanan = $buku->transaksis->where('jenis', 'simpanan')->sum('jumlah');
                $buku->totalPenarikanSimpanan = $buku->transaksis
                    ->where('jenis', 'penarikan')
                    ->where('sumber_penarikan', 'simpanan')
                    ->sum('jumlah');
            
                // Calculate total installments and withdrawals
                $buku->totalCicilan = $buku->transaksis->where('jenis', 'cicilan')->sum('jumlah');
                $buku->totalPenarikanCicilan = $buku->transaksis
                    ->where('jenis', 'penarikan')
                    ->where('sumber_penarikan', 'cicilan')
                    ->sum('jumlah');
            
                return $buku;
            });

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
    
        // Get the current total based on withdrawal source
        $totalSumber = Transaksi::where('buku_tabungan_id', $request->buku_tabungan_id)
            ->where('jenis', $request->sumber_penarikan)
            ->sum('jumlah');
    
        // Get total withdrawals for this source
        $totalPenarikan = Transaksi::where('buku_tabungan_id', $request->buku_tabungan_id)
            ->where('jenis', 'penarikan')
            ->where('sumber_penarikan', $request->sumber_penarikan)
            ->sum('jumlah');
    
        // Calculate available balance
        $saldoTersedia = $totalSumber - $totalPenarikan;
    
        // Validate available balance
        if ($saldoTersedia < $request->jumlah) {
            return back()->with('error', 'Saldo ' . $request->sumber_penarikan . ' tidak mencukupi! Saldo tersedia: ' . number_format($saldoTersedia, 2));
        }
    
        // Save withdrawal transaction
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

    public function edit(Transaksi $transaksi)
    {
        return view('transaksi.edit', compact('transaksi'));
    }

    public function update(Request $request, Transaksi $transaksi)
    {
        $request->validate([
            'jumlah' => 'required|numeric|min:1',
            'tanggal' => 'required|date',
            'keterangan' => 'nullable|string|max:255',
        ]);
    
        $transaksi->update([
            'jumlah' => $request->jumlah,
            'tanggal' => $request->tanggal,
            'keterangan' => $request->keterangan,
        ]);
    
        return redirect()
            ->route('transaksi.index')
            ->with('success', 'Transaksi berhasil diperbarui');
    }
}