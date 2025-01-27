<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id();
            
            // Relasi
            $table->foreignId('siswa_id')->constrained('siswa')->onDelete('cascade');
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajaran')->onDelete('cascade');
            
            // Jenis biaya & bulan hijriah
            $table->enum('jenis_biaya', [
                'SPP', 
                'IKK', 
                'THB', 
                'Uang Pangkal', 
                'Raport', 
                'Wisuda', 
                'Foto', 
                'Seragam'
            ]);
            
            $table->enum('bulan_hijri', [
                'Muharram',
                'Safar',
                'Rabiul Awwal',
                'Rabiul Akhir',
                'Jumadil Awwal',
                'Jumadil Akhir',
                'Rajab',
                "Sya\'ban",
                'Ramadan',
                'Syawwal',
                'Dzulqaidah',
                'Dzulhijjah'
            ])->nullable(); // Nullable untuk semua jenis kecuali SPP
            
            // Data pembayaran
            $table->decimal('jumlah', 15, 2);
            $table->enum('metode_pembayaran', ['cash', 'cicilan', 'tabungan']);
            $table->boolean('is_processed')->default(false);
            $table->text('keterangan')->nullable();
            $table->timestamps();

            // Unique: Hanya untuk SPP (kombinasi siswa + tahun + bulan)
            $table->unique(
                ['siswa_id', 'tahun_ajaran_id', 'jenis_biaya', 'bulan_hijri'],
                'unique_pembayaran_spp'
            )->where('jenis_biaya', 'SPP'); // Hanya berlaku untuk SPP

            // Unique untuk jenis biaya lain (tanpa bulan)
            $table->unique(
                ['siswa_id', 'tahun_ajaran_id', 'jenis_biaya'],
                'unique_pembayaran_non_spp'
            )->where('jenis_biaya', '<>', 'SPP'); // Kecuali SPP
        });
    }

    public function down()
    {
        Schema::dropIfExists('pembayaran');
    }
};