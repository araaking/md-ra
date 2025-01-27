<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('kelas', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('tingkat')->unsigned(); // Make it unsigned to allow 0
            $table->foreignId('next_class_id')
                ->nullable()
                ->constrained('kelas')
                ->onDelete('set null');
            $table->timestamps();

            // Kombinasi name + tingkat harus unik
            $table->unique(['name', 'tingkat']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('kelas');
    }
};