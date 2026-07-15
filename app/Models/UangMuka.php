<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UangMuka extends Model
{
    protected $fillable = [
        'proyek_id',
        'kontraktor_id',
        'tanggal_pengajuan',
        'nilai_pengajuan',
        'surat_permohonan',
        'status',
        'catatan_ppk',
    ];

    protected $casts = [
        'tanggal_pengajuan' => 'date',
        'nilai_pengajuan' => 'decimal:2',
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
