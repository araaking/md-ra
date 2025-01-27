<?php

namespace App\Http\Controllers;

use App\Models\TahunAjaran;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\BiayaSekolah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TahunAjaranController extends Controller
{
    public function index()
    {
        $tahunAjaran = TahunAjaran::orderBy('year_name', 'desc')->get();
        return view('tahun_ajaran.index', compact('tahunAjaran'));
    }

    public function create()
    {
        return view('tahun_ajaran.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'year_name' => 'required|string|max:255|unique:tahun_ajaran',
        ]);

        // Nonaktifkan semua tahun ajaran
        TahunAjaran::query()->update(['is_active' => false]);

        // Buat tahun ajaran baru
        $tahunBaru = TahunAjaran::create([
            'year_name' => $validated['year_name'],
            'is_active' => true
        ]);

        // Proses data tahun sebelumnya
        $tahunSebelumnya = TahunAjaran::where('is_active', false)
            ->orderBy('id', 'desc')
            ->first();

        if ($tahunSebelumnya) {
            $this->processClassPromotion($tahunBaru, $tahunSebelumnya);
            $this->copyBiayaSekolah($tahunBaru, $tahunSebelumnya); // Tambahkan ini
        }

        return redirect()->route('tahun-ajaran.index')
            ->with('success', 'Tahun ajaran berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $tahunAjaran = TahunAjaran::findOrFail($id);
        return view('tahun_ajaran.edit', compact('tahunAjaran'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'year_name' => 'required|string|max:255|unique:tahun_ajaran,year_name,' . $id,
        ]);

        $tahunAjaran = TahunAjaran::findOrFail($id);
        $tahunAjaran->update($validated);

        return redirect()->route('tahun-ajaran.index')
            ->with('success', 'Tahun ajaran berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $tahunAjaran = TahunAjaran::findOrFail($id);

        // Hapus data terkait
        Siswa::where('academic_year_id', $tahunAjaran->id)->delete();
        BiayaSekolah::where('tahun_ajaran_id', $tahunAjaran->id)->delete();
        $tahunAjaran->delete();

        return redirect()->route('tahun-ajaran.index')
            ->with('success', 'Tahun ajaran berhasil dihapus.');
    }

    /**
     * Proses kenaikan kelas otomatis
     */
    private function processClassPromotion(TahunAjaran $tahunBaru, TahunAjaran $tahunSebelumnya)
    {
        $siswaAktif = Siswa::with('kelas')
            ->where('academic_year_id', $tahunSebelumnya->id)
            ->where('status', 'Aktif')
            ->get();

        Log::info("Memproses kenaikan kelas untuk {$siswaAktif->count()} siswa.");

        foreach ($siswaAktif as $siswa) {
            $kelasSekarang = $siswa->kelas;
            $kelasTujuan = $kelasSekarang->nextClass;

            // Fallback jika tidak ada next_class_id
            if (!$kelasTujuan) {
                $kelasTujuan = Kelas::where('tingkat', $kelasSekarang->tingkat + 1)
                    ->orderBy('tingkat', 'asc')
                    ->first();
            }

            // Update data siswa
            if ($kelasTujuan) {
                $siswa->update([
                    'class_id' => $kelasTujuan->id,
                    'academic_year_id' => $tahunBaru->id,
                    'status' => 'Aktif'
                ]);
                Log::info("Siswa ID {$siswa->id} naik ke kelas {$kelasTujuan->name}");
            } else {
                $siswa->update([
                    'status' => 'Lulus',
                    'academic_year_id' => $tahunBaru->id
                ]);
                Log::info("Siswa ID {$siswa->id} lulus dari kelas {$kelasSekarang->name}");
            }
        }
    }

    /**
     * Copy biaya sekolah dari tahun sebelumnya
     */
    private function copyBiayaSekolah(TahunAjaran $tahunBaru, TahunAjaran $tahunLama)
    {
        $biayaLama = BiayaSekolah::where('tahun_ajaran_id', $tahunLama->id)->get();
        
        foreach ($biayaLama as $biaya) {
            BiayaSekolah::create([
                'tahun_ajaran_id' => $tahunBaru->id,
                'jenis_biaya' => $biaya->jenis_biaya,
                'kategori_siswa' => $biaya->kategori_siswa,
                'tingkat' => $biaya->tingkat,
                'jumlah' => $biaya->jumlah,
                'keterangan' => $biaya->keterangan
            ]);
        }
        
        Log::info("Berhasil menyalin {$biayaLama->count()} biaya sekolah dari tahun {$tahunLama->year_name}");
    }
}