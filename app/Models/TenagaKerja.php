<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TenagaKerja extends Model
{
    use HasFactory;

    protected $fillable = [
        'laporan_harian_id',
        'klasifikasi',
        'keterangan',
        'jumlah',
    ];

    public function laporanHarian()
    {
        return $this->belongsTo(LaporanHarian::class);
    }

    public function getKlasifikasiLabelAttribute(): string
    {
        return match($this->klasifikasi) {
            'mandor'           => 'Mandor',
            'tukang'           => 'Tukang',
            'pembantu_tukang'  => 'Pembantu Tukang',
            'sub_kontraktor'   => 'Sub-Kontraktor',
            'lainnya'          => 'Lainnya',
            default            => ucfirst($this->klasifikasi),
        };
    }
}
