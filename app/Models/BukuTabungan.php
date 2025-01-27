<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BukuTabungan extends Model
{
    use HasFactory;

    protected $table = 'buku_tabungan';

    // Kolom yang bisa diisi
    protected $fillable = [
        'siswa_id',
        'tahun_ajaran_id',
        'class_id', // Tambahan baru
        'nomor_urut'
    ];

    /* RELASI */
    
    // Relasi ke Tahun Ajaran
    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    // Relasi ke Siswa
    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    // Relasi ke Kelas (Tambahan baru)
    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'class_id');
    }

    // Relasi ke Transaksi
    public function transaksis()
    {
        return $this->hasMany(Transaksi::class);
    }
}