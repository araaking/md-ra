<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    /**
     * Menampilkan daftar kelas.
     */
    public function index()
    {
        $kelas = Kelas::all();
        return view('kelas.index', compact('kelas'));
    }

    /**
     * Menampilkan form untuk membuat kelas baru.
     */
    public function create()
{
    $kelas = Kelas::all(); // Ambil semua data kelas
    return view('kelas.create', compact('kelas'));
}


    /**
     * Menyimpan kelas baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'tingkat' => 'required|integer|min:0|max:6' // Updated validation rule
        ]);

        Kelas::create($request->all());

        return redirect()->route('kelas.index')
            ->with('success', 'Kelas berhasil ditambahkan.');
    }

    /**
     * Menampilkan form untuk mengedit kelas.
     */
    public function edit($id)
{
    $kelas = Kelas::findOrFail($id); // Data kelas yang sedang diedit
    $allKelas = Kelas::where('id', '!=', $id)->get(); // Ambil semua kelas kecuali yang sedang diedit
    return view('kelas.edit', compact('kelas', 'allKelas'));
}

    /**
     * Mengupdate kelas di database.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'tingkat' => 'required|integer|min:0|max:6' // Updated validation rule
        ]);

        $kelas = Kelas::findOrFail($id);
        $kelas->update($request->all());

        return redirect()->route('kelas.index')
            ->with('success', 'Kelas berhasil diperbarui.');
    }

    /**
     * Menghapus kelas dari database.
     */
    public function destroy($id)
    {
        $kelas = Kelas::findOrFail($id);
        $kelas->delete();

        return redirect()->route('kelas.index')
            ->with('success', 'Kelas berhasil dihapus.');
    }
}