<?php

namespace App\Http\Controllers;

use App\Models\BiayaSekolah;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;

class BiayaSekolahController extends Controller
{
    // Tampilkan semua biaya untuk tahun ajaran aktif
    public function index()
    {
        $tahunAktif = TahunAjaran::where('is_active', true)->firstOrFail();
        $biayas = BiayaSekolah::where('tahun_ajaran_id', $tahunAktif->id)
            ->orderBy('jenis_biaya')
            ->paginate(10);
            
        return view('biaya-sekolah.index', compact('biayas'));
    }

    // Form tambah biaya
    public function create()
    {
        return view('biaya-sekolah.create');
    }

    // Simpan biaya baru
    public function store(Request $request)
    {
        $request->validate([
            'jenis_biaya' => 'required|in:SPP,IKK,THB,UAM,Wisuda,Uang Pangkal,Raport,Seragam,Foto',
            'kategori_siswa' => 'required_if:jenis_biaya,SPP,IKK|nullable|in:Anak Guru,Anak Yatim,Kakak Beradik,Anak Normal',
            'tingkat' => 'required_if:jenis_biaya,THB|nullable|integer|min:1',
            'jumlah' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string'
        ]);

        BiayaSekolah::create([
            'tahun_ajaran_id' => TahunAjaran::getActive()->id,
            'jenis_biaya' => $request->jenis_biaya,
            'kategori_siswa' => $request->kategori_siswa,
            'tingkat' => $request->tingkat,
            'jumlah' => $request->jumlah,
            'keterangan' => $request->keterangan
        ]);

        return redirect()->route('biaya-sekolah.index')
            ->with('success', 'Biaya berhasil ditambahkan!');
    }

    // Form edit biaya
    public function edit($id)
    {
        $biaya = BiayaSekolah::findOrFail($id);
        return view('biaya-sekolah.edit', compact('biaya'));
    }

    // Update biaya
    public function update(Request $request, $id)
    {
        $request->validate([
            'jumlah' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string'
        ]);

        $biaya = BiayaSekolah::findOrFail($id);
        $biaya->update([
            'jumlah' => $request->jumlah,
            'keterangan' => $request->keterangan
        ]);

        return redirect()->route('biaya-sekolah.index')
            ->with('success', 'Biaya berhasil diperbarui!');
    }

    // Hapus biaya
    public function destroy($id)
    {
        BiayaSekolah::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Biaya dihapus!');
    }
}