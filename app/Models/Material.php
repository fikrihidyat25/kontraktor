<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Material extends Model
{
    use HasFactory;

    protected $fillable = [
        'laporan_harian_id',
        'jenis_material',
        'satuan',
        'kuantitas_datang',
        'kuantitas_digunakan',
        'keterangan',
    ];

    protected $casts = [
        'kuantitas_datang'    => 'decimal:2',
        'kuantitas_digunakan' => 'decimal:2',
    ];

    public function laporanHarian()
    {
        return $this->belongsTo(LaporanHarian::class);
    }
}
