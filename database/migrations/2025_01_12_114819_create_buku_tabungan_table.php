<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBukuTabunganTable extends Migration
{
    public function up()
    {
        Schema::create('buku_tabungan', function (Blueprint $table) {
            $table->id();
            
            // Foreign keys
            $table->foreignId('siswa_id')
                ->constrained('siswa')
                ->onDelete('cascade');
            
            $table->foreignId('tahun_ajaran_id')
                ->constrained('tahun_ajaran')
                ->onDelete('cascade');
            
            // Tambahan kolom class_id
            $table->foreignId('class_id')
                ->constrained('kelas')
                ->onDelete('cascade');
            
            // Nomor urut (unik per kelas + tahun ajaran)
            $table->integer('nomor_urut');
            
            // Unique constraint baru
            $table->unique(['class_id', 'tahun_ajaran_id', 'nomor_urut']);
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('buku_tabungan');
    }
}