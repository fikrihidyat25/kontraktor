<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Peralatan extends Model
{
    use HasFactory;

    protected $fillable = [
        'laporan_harian_id',
        'jenis_alat',
        'jumlah',
        'kondisi',
        'jam_operasi',
        'keterangan',
    ];

    protected $casts = [
        'jam_operasi' => 'decimal:2',
    ];

    public function laporanHarian()
    {
        return $this->belongsTo(LaporanHarian::class);
    }

    public function getKondisiLabelAttribute(): string
    {
        return match($this->kondisi) {
            'baik'              => 'Baik',
            'rusak_ringan'      => 'Rusak Ringan',
            'rusak_berat'       => 'Rusak Berat',
            'tidak_beroperasi'  => 'Tidak Beroperasi',
            default             => ucfirst($this->kondisi),
        };
    }

    public function getKondisiColorAttribute(): string
    {
        return match($this->kondisi) {
            'baik'             => 'text-green-700 bg-green-100',
            'rusak_ringan'     => 'text-amber-700 bg-amber-100',
            'rusak_berat'      => 'text-red-700 bg-red-100',
            'tidak_beroperasi' => 'text-gray-600 bg-gray-100',
            default            => 'text-gray-500',
        };
    }
}
