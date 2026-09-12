<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LaporanBulanan extends Model
{
    use HasFactory, \App\Traits\LogsActivity;

    protected $guarded = ['id'];
    
    protected $casts = [
        'dokumentasi' => 'array',
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
        return 'Bulan ke-' . $this->bulan;
    }
}
