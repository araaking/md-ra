<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\BukuTabungan;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil tahun ajaran aktif
        $tahunAktif = TahunAjaran::where('is_active', true)->firstOrFail();

        // 2. Setup periode tanggal untuk 7 hari terakhir
        $endDate = Carbon::today()->endOfDay();
        $startDate = Carbon::today()->subDays(6)->startOfDay();

        // 3. Hitung metrik utama
        $currentSimpanan = $this->hitungTransaksi('simpanan', $startDate, $endDate, $tahunAktif);
        $currentCicilan = $this->hitungTransaksi('cicilan', $startDate, $endDate, $tahunAktif);
        $currentPendapatan = $this->hitungPendapatan($tahunAktif);

        // 4. Total siswa aktif
        $totalSiswa = Siswa::where('status', 'Aktif')
            ->where('academic_year_id', $tahunAktif->id)
            ->count();

        // 5. Data tabel dengan filter
        $dataTable = $this->prosesDataTable($request, $tahunAktif);

        return view('dashboard', array_merge([
            'totalSimpanan' => $currentSimpanan,
            'totalCicilan' => $currentCicilan,
            'totalPendapatan' => $currentPendapatan,
            'totalSiswa' => $totalSiswa,
        ], $dataTable));
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