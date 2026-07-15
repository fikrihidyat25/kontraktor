<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LaporanMingguan extends Model
{
    use HasFactory;

    protected $fillable = [
        'proyek_id',
        'kontraktor_id',
        'minggu_ke',
        'tanggal_mulai',
        'tanggal_selesai',
        'bobot_rencana',
        'bobot_realisasi',
        'deviasi',
        'ringkasan_kemajuan',
        'kendala',
        'file_laporan',
        'status',
        'catatan_konsultan',
        'verified_by',
        'verified_at',
        'catatan_ppk',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'tanggal_mulai'   => 'date',
        'tanggal_selesai' => 'date',
        'verified_at'     => 'datetime',
        'approved_at'     => 'datetime',
        'bobot_rencana'   => 'decimal:2',
        'bobot_realisasi' => 'decimal:2',
        'deviasi'         => 'decimal:2',
    ];

    public function proyek()
    {
        return $this->belongsTo(Proyek::class);
    }

    public function kontraktor()
    {
        return $this->belongsTo(User::class, 'kontraktor_id');
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'draft'     => 'Draft',
            'submitted' => 'Menunggu Verifikasi',
            'verified'  => 'Terverifikasi',
            'approved'  => 'Disetujui',
            'rejected'  => 'Ditolak',
            default     => ucfirst($this->status),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'draft'     => 'text-gray-500 bg-gray-100',
            'submitted' => 'text-amber-700 bg-amber-100',
            'verified'  => 'text-blue-700 bg-blue-100',
            'approved'  => 'text-green-700 bg-green-100',
            'rejected'  => 'text-red-700 bg-red-100',
            default     => 'text-gray-500 bg-gray-100',
        };
    }

    public function getDeviasiColorAttribute(): string
    {
        if ($this->deviasi >= 0) return 'text-green-600';
        if ($this->deviasi >= -5) return 'text-amber-600';
        return 'text-red-600';
    }
}
