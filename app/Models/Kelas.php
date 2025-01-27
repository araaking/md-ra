<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'tingkat',
        'next_class_id' // Tambahkan ini untuk relasi nextClass
    ];

    /**
     * Relasi ke kelas berikutnya
     */
    public function nextClass()
{
    return $this->belongsTo(Kelas::class, 'next_class_id');
}

    /**
     * Relasi inverse ke kelas sebelumnya
     */
    public function previousClasses()
    {
        return $this->hasMany(Kelas::class, 'next_class_id');
    }

    /**
     * Relasi ke siswa yang berada di kelas ini
     */
    public function siswa()
    {
        return $this->hasMany(Siswa::class, 'class_id');
    }

    /**
     * Relasi ke tahun ajaran melalui siswa (jika diperlukan)
     */
    public function tahunAjaran()
    {
        return $this->hasManyThrough(
            TahunAjaran::class,
            Siswa::class,
            'class_id', // Foreign key di tabel siswa
            'id', // Foreign key di tabel tahun_ajaran
            'id', // Local key di tabel kelas
            'academic_year_id' // Local key di tabel siswa
        );
    }
}