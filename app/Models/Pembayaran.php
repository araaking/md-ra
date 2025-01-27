<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class Pembayaran extends Model
{
    use HasFactory;

    protected $table = 'pembayaran';

    protected $fillable = [
        'siswa_id',
        'tahun_ajaran_id',
        'jenis_biaya',
        'bulan_hijri',
        'jumlah',
        'metode_pembayaran',
        'is_processed',
        'keterangan'
    ];

    protected $casts = [
        'is_processed' => 'boolean',
        'jumlah' => 'decimal:2'
    ];

    // Daftar jenis biaya yang valid
    const JENIS_BIAYA = [
        'SPP', 
        'IKK', 
        'THB', 
        'Uang Pangkal', 
        'Raport', 
        'Wisuda', 
        'Foto', 
        'Seragam'
    ];

    // Daftar bulan Hijriah
    const BULAN_HIJRI = [
        'Muharram',
        'Safar',
        'Rabiul Awwal',
        'Rabiul Akhir',
        'Jumadil Awwal',
        'Jumadil Akhir',
        'Rajab',
        'Sya\'ban',
        'Ramadan',
        'Syawwal',
        'Dzulqaidah',
        'Dzulhijjah'
    ];

    /* ================== RELASI ================== */
    
    /**
     * Relasi ke model Siswa.
     */
    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    /**
     * Relasi ke model TahunAjaran.
     */
    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    /**
     * Relasi ke model Transaksi untuk cicilan.
     */
    public function transaksiCicilan()
    {
        return $this->hasOne(Transaksi::class, 'pembayaran_id')
            ->where('jenis', 'cicilan');
    }

    /* ================== SCOPES ================== */
    
    /**
     * Scope untuk pembayaran SPP.
     */
    public function scopeSPP($query)
    {
        return $query->where('jenis_biaya', 'SPP');
    }

    /**
     * Scope untuk pembayaran yang belum diproses.
     */
    public function scopeBelumDiproses($query)
    {
        return $query->where('is_processed', false);
    }

    /**
     * Scope untuk pembayaran via tabungan.
     */
    public function scopeViaTabungan($query)
    {
        return $query->where('metode_pembayaran', 'tabungan');
    }

    /**
     * Scope untuk pembayaran berdasarkan siswa tertentu.
     */
    public function scopeUntukSiswa($query, $siswaId)
    {
        return $query->where('siswa_id', $siswaId);
    }

    /* ================== METHODS ================== */
    
    /**
     * Mendapatkan label lengkap untuk jenis biaya.
     */
    public function getLabelJenisBiaya()
    {
        return match($this->jenis_biaya) {
            'SPP' => 'SPP (Sumbangan Pembinaan Pendidikan)',
            'IKK' => 'IKK (Iuran Kebersihan dan Keamanan)',
            'THB' => 'THB (Tahunan Harian Bulanan)',
            default => $this->jenis_biaya
        };
    }

    /**
     * Accessor untuk bulan Hijriah.
     */
    public function getBulanHijriAttribute($value)
    {
        if ($this->jenis_biaya === 'SPP') {
            return $value . ' ' . $this->tahunAjaran->year_name;
        }
        return '-';
    }

    /**
     * Mendapatkan detail pembayaran dalam bentuk array.
     */
    public function getDetailPembayaranAttribute()
    {
        return [
            'jumlah' => $this->jumlah,
            'metode' => ucfirst($this->metode_pembayaran),
            'status' => $this->is_processed ? 'Terverifikasi' : 'Menunggu Verifikasi'
        ];
    }

    /**
     * Menandai pembayaran sebagai diproses.
     */
    public function markAsProcessed()
    {
        $this->update(['is_processed' => true]);
    }

    /* ================== VALIDASI ================== */
    
    /**
     * Aturan validasi untuk pembayaran.
     */
    public static function validationRules($jenisBiaya = null)
    {
        return [
            'siswa_id' => 'required|exists:siswa,id',
            'jenis_biaya' => 'required|in:' . implode(',', self::JENIS_BIAYA),
            'bulan_hijri' => [
                Rule::requiredIf($jenisBiaya === 'SPP'),
                'nullable',
                'in:' . implode(',', self::BULAN_HIJRI)
            ],
            'jumlah' => 'required|numeric|min:0',
            'metode_pembayaran' => 'required|in:cash,cicilan,tabungan',
            'keterangan' => 'nullable|string|max:255'
        ];
    }
}