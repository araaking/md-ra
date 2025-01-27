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