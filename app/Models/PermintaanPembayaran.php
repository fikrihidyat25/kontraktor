<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermintaanPembayaran extends Model
{
    protected $fillable = [
        'proyek_id',
        'kontraktor_id',
        'nomor_tagihan',
        'tanggal_pengajuan',
        'termin_ke',
        'nilai_tagihan',
        'persentase_kemajuan',
        'status',
        'catatan',
    ];

    protected $casts = [
        'tanggal_pengajuan' => 'date',
        'nilai_tagihan' => 'decimal:2',
        'persentase_kemajuan' => 'decimal:2',
    ];

    public function proyek()
    {
        return $this->belongsTo(Proyek::class);
    }

    public function kontraktor()
    {
        return $this->belongsTo(User::class, 'kontraktor_id');
    }
}
