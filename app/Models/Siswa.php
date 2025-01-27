<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    use HasFactory;

    protected $table = 'siswa';

    protected $fillable = [
        'name', 
        'nis', 
        'class_id', 
        'academic_year_id', // Tambahkan ini
        'status', 
        'category', 
        'remarks'
    ];

    protected $casts = [
        'status' => 'string',
        'category' => 'string'
    ];

    // Relasi ke Kelas
    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'class_id');
    }

    // Relasi ke Tahun Ajaran
    public function academicYear()
    {
        return $this->belongsTo(TahunAjaran::class, 'academic_year_id');
    }

    public function bukuTabungans()
    {
        return $this->hasMany(BukuTabungan::class);
    }

    public function pembayarans()
    {
        return $this->hasMany(Pembayaran::class);
    }
}