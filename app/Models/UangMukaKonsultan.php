<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use App\Traits\LogsActivity;

class UangMukaKonsultan extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'proyek_id',
        'konsultan_id',
        'nilai_pengajuan',
        'keterangan',
        'dokumen_lampiran',
        'status',
        'catatan_ppk',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'nilai_pengajuan' => 'decimal:2',
        'dokumen_lampiran' => 'array',
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

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
