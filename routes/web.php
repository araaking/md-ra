<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TahunAjaranController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\BukuTabunganController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BiayaSekolahController;
use App\Http\Controllers\PembayaranController;
use Illuminate\Support\Facades\Route;

// Route utama
Route::get('/', function () {
    return redirect()->route('login');
});

// Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Route dengan middleware auth
Route::middleware('auth')->group(function () {
    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Tahun Ajaran
    Route::resource('tahun-ajaran', TahunAjaranController::class)->except(['show']);

    // Kelas
    Route::resource('kelas', KelasController::class);

    // Siswa
    Route::resource('siswa', SiswaController::class);

    // Buku Tabungan
    Route::resource('buku-tabungan', BukuTabunganController::class);

    /* ----- Route Transaksi & Penarikan ----- */
    // Penarikan harus didefinisikan SEBELUM resource
    Route::prefix('transaksi')->group(function () {
        Route::get('penarikan', [TransaksiController::class, 'createPenarikan'])
            ->name('transaksi.penarikan.create');
        Route::post('penarikan', [TransaksiController::class, 'storePenarikan'])
            ->name('transaksi.penarikan.store');
    });

    Route::resource('biaya-sekolah', BiayaSekolahController::class);

    // Resource Transaksi (setelah penarikan)
    Route::resource('transaksi', TransaksiController::class);

    /* ----- Route Khusus AJAX ----- */
    Route::get('/kelas/{kelas}/siswa', [SiswaController::class, 'getSiswaByKelas'])
        ->name('kelas.siswa.ajax');
        
    Route::get('/get-buku-tabungan-by-kelas/{kelasId}', [TransaksiController::class, 'getBukuTabunganByKelas'])
        ->name('get-buku-tabungan-by-kelas');
    
    Route::resource('pembayaran', PembayaranController::class);
    
    Route::get('/pembayaran/export-pdf/{siswa_id}', [PembayaranController::class, 'exportPDF'])
    ->name('pembayaran.export-pdf');
});

require __DIR__.'/auth.php';