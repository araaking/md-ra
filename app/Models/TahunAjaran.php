<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TahunAjaran extends Model
{
    use HasFactory;

    protected $table = 'tahun_ajaran';

    // Sesuaikan dengan nama kolom di migrasi
    protected $fillable = [
        'year_name', 
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    // Accessor untuk kolom year_name (opsional)
    public function getNamaAttribute()
    {
        return $this->year_name;
    }

    // Mutator untuk kolom year_name (opsional)
    public function setNamaAttribute($value)
    {
        $this->attributes['year_name'] = $value;
    }

    // Relasi ke BukuTabungan
    public function bukuTabungans()
    {
        return $this->hasMany(BukuTabungan::class);
    }

    // Method untuk nonaktifkan tahun ajaran lain
    public function deactivateOthers(): void
    {
        if ($this->is_active) {
            self::query()
                ->where('id', '!=', $this->id)
                ->where('is_active', true)
                ->update(['is_active' => false]);
        }
    }

    public static function getActive()
    {
        return self::where('is_active', true)->firstOrFail();
    }

    // Booted method
    protected static function booted()
    {
        static::saving(function ($tahunAjaran) {
            if ($tahunAjaran->is_active) {
                $tahunAjaran->deactivateOthers();
            }
        });
    }
}