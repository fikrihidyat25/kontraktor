<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SerahTerima extends Model
{
    protected $fillable = [
        'proyek_id',
        'kontraktor_id',
        'jenis',
        'tanggal_pengajuan',
        'keterangan',
        'surat_permohonan',
        'dokumen_lampiran',
        'surat_persetujuan_konsultan',
        'status_konsultan',
        'catatan_konsultan',
        'status',
        'status_pptk',
        'catatan_pptk',
        'catatan_ppk',
        'surat_persetujuan_ppk',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'tanggal_pengajuan' => 'date',
        'approved_at' => 'datetime',
        'dokumen_lampiran' => 'array',
    ];

    public function proyek()
    {
        return $this->belongsTo(Proyek::class);
    }

    public function kontraktor()
    {
        return $this->belongsTo(User::class, 'kontraktor_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
