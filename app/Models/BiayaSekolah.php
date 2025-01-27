<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BiayaSekolah extends Model
{
    use HasFactory;

    protected $table = 'biaya_sekolah';
    
    protected $fillable = [
        'tahun_ajaran_id',
        'jenis_biaya',
        'kategori_siswa',
        'tingkat',
        'jumlah',
        'keterangan'
    ];

    public static function needsStudentCategory($jenisBiaya)
    {
        return in_array($jenisBiaya, ['SPP', 'IKK', 'Uang Pangkal']);
    }

    public static function needsClassLevel($jenisBiaya)
    {
        return in_array($jenisBiaya, ['THB', 'UAM', 'Foto']);
    }

    public static function getAvailableClasses($jenisBiaya)
    {
        switch ($jenisBiaya) {
            case 'THB':
                return ['2', '3', '4', '5', '6', '7']; // Updated for new level system
            case 'UAM':
                return ['7']; // Grade 6 is now level 7
            case 'Foto':
                return ['1', '7']; // 1=TK, 7=Grade 6
            default:
                return [];
        }
    }

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    public static function getBiaya($jenisBiaya, $kategoriSiswa)
{
    return self::where('jenis_biaya', $jenisBiaya)
        ->where('kategori_siswa', $kategoriSiswa)
        ->value('jumlah') ?? 0;
}
}