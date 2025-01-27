<?php
// File: database/migrations/xxxx_xx_xx_create_siswa_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSiswaTable extends Migration
{
    public function up()
    {
        Schema::create('siswa', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('nis', 20)->unique()->nullable();
            $table->foreignId('class_id')->constrained('kelas')->onDelete('cascade');
            $table->foreignId('academic_year_id')->constrained('tahun_ajaran')->onDelete('cascade');
            $table->enum('status', ['Aktif', 'Lulus', 'Keluar'])->default('Aktif');
            $table->enum('category', ['Anak Guru', 'Anak Yatim', 'Kakak Beradik', 'Anak Normal']);
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('siswa');
    }
}