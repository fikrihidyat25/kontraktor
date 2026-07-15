<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaporanBulanan extends Model
{
    protected $guarded = ['id'];
    
    protected $casts = [
        'verified_at' => 'datetime',
        'approved_at' => 'datetime',
        'bobot_rencana' => 'decimal:2',
        'bobot_realisasi' => 'decimal:2',
        'deviasi' => 'decimal:2',
    ];

    public function proyek()
    {
        return $this->belongsTo(Proyek::class);
    }

    public function kontraktor()
    {
        return $this->belongsTo(User::class, 'kontraktor_id');
    }

    public function verifikator()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function getBulanLabelAttribute()
    {
        $bulanList = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        return $bulanList[$this->bulan] ?? '-';
    }
}
