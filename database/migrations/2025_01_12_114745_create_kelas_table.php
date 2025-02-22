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
            $table->integer('tingkat')->unsigned()->comment('1=TK, 2-7=Grade 1-6'); // Updated comment
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