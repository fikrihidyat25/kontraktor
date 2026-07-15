<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RealisasiBiaya extends Model
{
    use HasFactory;

    protected $fillable = [
        'laporan_harian_id',
        'divisi_pekerjaan',
        'nilai_realisasi',
        'bobot_fisik',
        'keterangan',
    ];

    protected $casts = [
        'nilai_realisasi' => 'decimal:2',
        'bobot_fisik'     => 'decimal:2',
    ];

    public function laporanHarian()
    {
        return $this->belongsTo(LaporanHarian::class);
    }
}
