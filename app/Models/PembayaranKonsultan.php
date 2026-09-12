<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\LogsActivity;

class PembayaranKonsultan extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'proyek_id',
        'konsultan_id',
        'termin',
        'nilai_pembayaran',
        'surat_permohonan',
        'laporan_kemajuan',
        'lampiran_lainnya',
        'status',
        'catatan_pptk',
        'catatan_ppk',
        'verified_by',
        'verified_at',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'nilai_pembayaran' => 'decimal:2',
        'lampiran_lainnya' => 'array',
        'verified_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    public function proyek()
    {
        return $this->belongsTo(Proyek::class);
    }

    public function konsultan()
    {
        return $this->belongsTo(User::class, 'konsultan_id');
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
