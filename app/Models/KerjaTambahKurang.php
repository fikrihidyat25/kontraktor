<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KerjaTambahKurang extends Model
{
    protected $fillable = [
        'proyek_id',
        'kontraktor_id',
        'nomor_surat_pengajuan',
        'tanggal_pengajuan',
        'jenis_ktk',
        'deskripsi_pekerjaan',
        'nilai_estimasi',
        'status',
        'catatan_konsultan',
        'catatan_ppk',
    ];

    protected $casts = [
        'tanggal_pengajuan' => 'date',
        'nilai_estimasi' => 'decimal:2',
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
