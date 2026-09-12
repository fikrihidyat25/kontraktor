<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\LogsActivity;

class PermintaanPembayaran extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'proyek_id',
        'kontraktor_id',
        'pembayaran_ke',
        'progres_tagihan',
        'nomor_tagihan',
        'tanggal_pengajuan',
        'nilai_tagihan',
        'status',
        'catatan',
        'dokumen_pendukung',
    ];

    protected $casts = [
        'tanggal_pengajuan' => 'date',
        'nilai_tagihan' => 'decimal:2',
        'progres_tagihan' => 'decimal:2',
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
