<?php

namespace App\Http\Controllers;

use App\Models\BukuTabungan;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Database\Query\Builder;

class BukuTabunganController extends Controller
{
    // Tampilkan semua buku tabungan
    public function index(Request $request)
        {
            // Get active academic year
            $tahunAktif = TahunAjaran::where('is_active', true)->firstOrFail();
            
            // Get all academic years for filter, ordered by year_name instead
            $tahunAjarans = TahunAjaran::orderBy('year_name', 'desc')->get();
            
            // Get selected year or default to active year
            $selectedYear = $request->tahun_ajaran_id ?? $tahunAktif->id;
    
            $bukuTabungans = BukuTabungan::with(['siswa', 'tahunAjaran', 'kelas'])
                ->where('tahun_ajaran_id', $selectedYear)
                ->orderBy('class_id')
                ->orderBy('nomor_urut')
                ->paginate(10);
    
            return view('buku_tabungan.index', compact('bukuTabungans', 'tahunAktif', 'tahunAjarans', 'selectedYear'));
        }

    // Form tambah buku tabungan
    public function create(Request $request)
    {
        // Cek tahun ajaran aktif
        $tahunAktif = TahunAjaran::where('is_active', true)->first();

        if (!$tahunAktif) {
            return redirect()->route('tahun-ajaran.index')
                ->with('error', 'Tidak ada tahun ajaran aktif!');
        }

        $kelasList = Kelas::all();
        $selectedKelasId = $request->input('kelas_id');
        $siswas = [];

        // Ambil siswa yang BELUM punya buku di tahun ini
        if ($selectedKelasId) {
            $siswas = Siswa::where('class_id', $selectedKelasId)
                ->where('academic_year_id', $tahunAktif->id) // Pastikan sesuai tahun ajaran aktif
                ->where('status', 'Aktif')
                ->whereNotIn('id', function ($query) use ($tahunAktif) {
                    $query->select('siswa_id')
                        ->from('buku_tabungan')
                        ->where('tahun_ajaran_id', $tahunAktif->id);
                })
                ->get();
        }

        return view('buku_tabungan.create', compact(
            'kelasList',
            'tahunAktif',
            'siswas',
            'selectedKelasId'
        ));
    }

    // Simpan buku tabungan baru
    public function store(Request $request)
    {
        $tahunAktif = TahunAjaran::where('is_active', true)->firstOrFail();

        $request->validate([
            'kelas_id'    => 'required|exists:kelas,id',
            'siswa_id'    => [
                'required',
                'exists:siswa,id',
                // Pastikan siswa belum punya buku di tahun ini
                Rule::unique('buku_tabungan')->where(function ($query) use ($tahunAktif) {
                    return $query->where('tahun_ajaran_id', $tahunAktif->id);
                })
            ],
            'nomor_urut' => [
                'required',
                'integer',
                // Pastikan nomor urut unik per kelas + tahun
                Rule::unique('buku_tabungan')->where(function ($query) use ($request, $tahunAktif) {
                    return $query->where('class_id', $request->kelas_id)
                        ->where('tahun_ajaran_id', $tahunAktif->id);
                })
            ],
        ]);

        BukuTabungan::create([
            'siswa_id'         => $request->siswa_id,
            'class_id'         => $request->kelas_id,
            'tahun_ajaran_id'  => $tahunAktif->id,
            'nomor_urut'       => $request->nomor_urut
        ]);

        return redirect()->route('buku-tabungan.index')
            ->with('success', 'Buku tabungan berhasil dibuat!');
    }

    // Form edit buku tabungan
    public function edit($id)
    {
        $bukuTabungan = BukuTabungan::findOrFail($id);
        $kelasList = Kelas::all();
        $tahunAjarans = TahunAjaran::all();

        // Ambil siswa dari kelas asli (sebelum edit)
        $siswas = Siswa::where('class_id', $bukuTabungan->class_id)
            ->where('academic_year_id', $bukuTabungan->tahun_ajaran_id)
            ->where('status', 'Aktif')
            ->get();

        return view('buku_tabungan.edit', compact(
            'bukuTabungan',
            'kelasList',
            'tahunAjarans',
            'siswas'
        ));
    }

    // Update buku tabungan
    public function update(Request $request, BukuTabungan $bukuTabungan)
    {
        $request->validate([
            'nomor_urut' => [
                'required',
                'numeric',
                'min:1',
                Rule::unique('buku_tabungan')
                    ->where('tahun_ajaran_id', $bukuTabungan->tahun_ajaran_id)
                    ->where('class_id', $bukuTabungan->class_id)  // Changed from kelas_id to class_id
                    ->ignore($bukuTabungan->id)
            ],
        ]);
    
        $bukuTabungan->update([
            'nomor_urut' => $request->nomor_urut,
        ]);
    
        return redirect()
            ->route('buku-tabungan.index')
            ->with('success', 'Nomor buku tabungan berhasil diperbarui');
    }

    // Hapus buku tabungan
    public function destroy($id)
    {
        $bukuTabungan = BukuTabungan::findOrFail($id);
        $bukuTabungan->delete();

        return redirect()->route('buku-tabungan.index')
            ->with('success', 'Buku tabungan berhasil dihapus!');
    }
}