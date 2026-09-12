<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class KerjaTambahKurang extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'proyek_id',
        'kontraktor_id',
        'nomor_surat_pengajuan',
        'tanggal_pengajuan',
        'usulan_dari',
        'jenis_ktk',
        'deskripsi_pekerjaan',
        'nilai_estimasi',
        'dokumen_pendukung',
        'status',
        'catatan_konsultan',
        'catatan_ppk',
    ];

    protected $casts = [
        'tanggal_pengajuan' => 'date',
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
