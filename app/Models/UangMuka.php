<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\LogsActivity;

class UangMuka extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'proyek_id',
        'kontraktor_id',
        'tanggal_pengajuan',
        'nilai_pengajuan',
        'surat_permohonan',
        'lampiran',
        'status',
        'catatan_ppk',
    ];

    protected $casts = [
        'tanggal_pengajuan' => 'date',
        'nilai_pengajuan' => 'decimal:2',
        'lampiran' => 'array',
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
