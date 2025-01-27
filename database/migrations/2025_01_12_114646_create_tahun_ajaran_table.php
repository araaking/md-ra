<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTahunAjaranTable extends Migration
{
    public function up()
    {
        Schema::create('tahun_ajaran', function (Blueprint $table) {
            $table->id();
            $table->string('year_name')->unique()->comment('Format: "YYYY/YYYY" contoh: "2023/2024"');
            $table->boolean('is_active')->default(false)->comment('Hanya 1 tahun ajaran aktif dalam sistem');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tahun_ajaran');
    }
}
