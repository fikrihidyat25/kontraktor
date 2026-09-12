<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\LogsActivity;

class SerahTerimaKonsultan extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'proyek_id',
        'konsultan_id',
        'jenis',
        'tanggal_pengajuan',
        'keterangan',
        'surat_permohonan',
        'dokumen_lampiran',
        'status',
        'catatan_ppk',
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

    public function konsultan()
    {
        return $this->belongsTo(User::class, 'konsultan_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
