<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('biaya_sekolah', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke tahun ajaran
            $table->foreignId('tahun_ajaran_id')
                ->constrained('tahun_ajaran')
                ->onDelete('cascade');
            
            // Jenis biaya lengkap
            $table->enum('jenis_biaya', [
                'SPP',
                'IKK',
                'THB',
                'UAM',
                'Wisuda',
                'Uang Pangkal',
                'Raport',
                'Seragam',
                'Foto'
            ]);
            
            // Kategori siswa (nullable untuk biaya yang tidak memerlukan kategori)
            $table->enum('kategori_siswa', [
                'Anak Guru',
                'Anak Yatim',
                'Kakak Beradik',
                'Anak Normal'
            ])->nullable();
            
            // Tingkat kelas (hanya untuk THB)
            $table->integer('tingkat')->nullable()->comment('Hanya untuk biaya THB');
            
            // Jumlah biaya
            $table->decimal('jumlah', 15, 2);
            
            // Keterangan tambahan
            $table->text('keterangan')->nullable();
            
            $table->timestamps();

            // Unique constraint untuk kombinasi tertentu
            $table->unique([
                'tahun_ajaran_id',
                'jenis_biaya', 
                'kategori_siswa',
                'tingkat'
            ], 'biaya_unik');
        });
    }

    public function down()
    {
        Schema::dropIfExists('biaya_sekolah');
    }
};