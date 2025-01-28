<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\BukuTabungan;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil tahun ajaran aktif
        $tahunAktif = TahunAjaran::where('is_active', true)->firstOrFail();
        
        // Add this to get all classes
        $kelas = Kelas::orderBy('tingkat')->get();
    
        // 2. Setup periode tanggal untuk 7 hari terakhir
        $endDate = Carbon::today()->endOfDay();
        $startDate = Carbon::today()->subDays(6)->startOfDay();
    
        // 3. Hitung metrik utama
        $totalSimpanan = $this->hitungTransaksi('simpanan', $startDate, $endDate, $tahunAktif);
        $totalCicilan = $this->hitungTransaksi('cicilan', $startDate, $endDate, $tahunAktif);
        $totalPendapatan = $this->hitungPendapatan($tahunAktif);
    
        // 4. Total siswa aktif
        $totalSiswa = Siswa::where('status', 'Aktif')
            ->where('academic_year_id', $tahunAktif->id)
            ->count();
    
        // 5. Get BukuTabungan data with all necessary relationships and calculations
        $bukuTabungans = BukuTabungan::query()
            ->where('tahun_ajaran_id', $tahunAktif->id)
            ->whereHas('siswa', function($query) {
                $query->where('status', 'Aktif');
            })
            ->when($request->kelas, function($query, $kelas) {
                $query->whereHas('siswa', function($q) use ($kelas) {
                    $q->where('class_id', $kelas);
                });
            })
            ->when($request->search, function($query, $search) {
                $query->where(function($q) use ($search) {
                    $q->whereHas('siswa', function($sq) use ($search) {
                        $sq->where('name', 'like', "%{$search}%");
                    })->orWhere('nomor_urut', 'like', "%{$search}%");
                });
            })
            ->with(['siswa.kelas', 'transaksis'])
            ->get()
            ->map(function ($buku) {
                // Calculate savings totals
                $buku->total_simpanan = $buku->transaksis
                    ->where('jenis', 'simpanan')
                    ->sum('jumlah');
                
                $buku->total_penarikan_simpanan = $buku->transaksis
                    ->where('jenis', 'penarikan')
                    ->where('sumber_penarikan', 'simpanan')
                    ->sum('jumlah');
    
                // Calculate installment totals
                $buku->total_cicilan = $buku->transaksis
                    ->where('jenis', 'cicilan')
                    ->sum('jumlah');
                
                $buku->total_penarikan_cicilan = $buku->transaksis
                    ->where('jenis', 'penarikan')
                    ->where('sumber_penarikan', 'cicilan')
                    ->sum('jumlah');
    
                return $buku;
            });
    
        // Paginate the collection
        $perPage = 10;
        $page = $request->get('page', 1);
        $bukuTabungans = new LengthAwarePaginator(
            $bukuTabungans->forPage($page, $perPage),
            $bukuTabungans->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );
    
        return view('dashboard', compact(
            'totalSimpanan',
            'totalCicilan',
            'totalPendapatan',
            'totalSiswa',
            'bukuTabungans',
            'kelas' // Add this
        ));
    }

    private function hitungTransaksi($jenis, $start, $end, $tahunAktif)
    {
        return Transaksi::where('jenis', $jenis)
            ->whereBetween('tanggal', [
                $start->format('Y-m-d H:i:s'),
                $end->format('Y-m-d H:i:s')
            ])
            ->whereHas('bukuTabungan', function($q) use ($tahunAktif) {
                $q->where('tahun_ajaran_id', $tahunAktif->id)
                  ->whereHas('siswa', function($q) {
                      $q->where('status', 'Aktif');
                  });
            })
            ->sum('jumlah');
    }

    private function hitungPendapatan($tahunAktif)
    {
        return BukuTabungan::with(['transaksis' => function($query) {
                $query->where('jenis', 'simpanan');
            }])
            ->where('tahun_ajaran_id', $tahunAktif->id)
            ->whereHas('siswa', function($q) {
                $q->where('status', 'Aktif');
            })
            ->get()
            ->sum(function($book) {
                return $book->transaksis->sum('jumlah') * 0.08;
            });
    }

    private function prosesDataTable($request, $tahunAktif)
    {
        $query = Transaksi::with([
                'bukuTabungan.siswa.kelas',
                'bukuTabungan.transaksis'
            ])
            ->whereHas('bukuTabungan', function($q) use ($tahunAktif) {
                $q->where('tahun_ajaran_id', $tahunAktif->id)
                  ->whereHas('siswa', function($q) {
                      $q->where('status', 'Aktif');
                  });
            });

        // Filter kelas
        if ($request->filled('kelas')) {
            $query->whereHas('bukuTabungan.siswa.kelas', function($q) use ($request) {
                $q->where('name', $request->kelas);
            });
        }

        // Filter pencarian
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->whereHas('bukuTabungan.siswa', function($q) use ($request) {
                    $q->where('name', 'like', '%'.$request->search.'%');
                })->orWhereHas('bukuTabungan', function($q) use ($request) {
                    $q->where('nomor_urut', 'like', '%'.$request->search.'%');
                });
            });
        }

        // Proses data
        $transaksiAll = $query->get()
            ->groupBy('bukuTabungan.siswa.id')
            ->map(function ($transaksiGroup) {
                $bukuTabungan = $transaksiGroup->first()->bukuTabungan;
                
                // Validasi relasi
                if(!$bukuTabungan || !$bukuTabungan->siswa || !$bukuTabungan->siswa->kelas) {
                    return null;
                }

                $totalTabungan = $bukuTabungan->transaksis
                    ->where('jenis', 'simpanan')
                    ->sum('jumlah');

                $totalCicilan = $bukuTabungan->transaksis
                    ->where('jenis', 'cicilan')
                    ->sum('jumlah');

                return (object)[
                    'id' => $bukuTabungan->id,
                    'nomor_tabungan' => $bukuTabungan->nomor_urut,
                    'nama' => $bukuTabungan->siswa->name,
                    'kelas' => $bukuTabungan->siswa->kelas->name,
                    'total_tabungan' => $totalTabungan,
                    'total_cicilan' => $totalCicilan,
                    'total_keseluruhan' => ($totalTabungan * 0.92) - $totalCicilan,
                ];
            })
            ->filter()
            ->values();

        // Pagination
        $page = $request->get('page', 1);
        $perPage = 10;
        
        return [
            'transaksis' => new LengthAwarePaginator(
                $transaksiAll->forPage($page, $perPage),
                $transaksiAll->count(),
                $perPage,
                $page,
                ['path' => $request->url(), 'query' => $request->query()]
            )
        ];
    }
}