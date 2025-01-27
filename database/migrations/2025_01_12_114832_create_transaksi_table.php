<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('transaksi', function (Blueprint $table) {
            // Kolom utama
            $table->id();
            $table->enum('jenis', ['simpanan', 'penarikan', 'cicilan'])
                ->comment('Jenis transaksi: simpanan/penarikan/cicilan');
            
            // Relasi ke buku tabungan
            $table->foreignId('buku_tabungan_id')
                ->constrained('buku_tabungan')
                ->onDelete('cascade')
                ->comment('ID buku tabungan terkait');

            // Data transaksi
            $table->decimal('jumlah', 15, 2)
                ->comment('Nominal transaksi (15 digit, 2 desimal)');
            $table->timestamp('tanggal')
                ->useCurrent()
                ->comment('Tanggal transaksi (otomatis terisi saat dibuat)');
            
            // Khusus penarikan
            $table->string('sumber_penarikan')
                ->nullable()
                ->comment('Sumber penarikan: simpanan/cicilan (hanya untuk jenis penarikan)');
            
            // Deskripsi
            $table->text('keterangan')
                ->nullable()
                ->comment('Catatan tambahan transaksi');

            // Timestamp
            $table->timestamps();

            // Index untuk optimasi query
            $table->index(['buku_tabungan_id', 'jenis']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('transaksi');
    }
};