<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class LaporanLainnya extends Model
{
    use LogsActivity;
    protected $fillable = [
        'proyek_id',
        'judul_laporan',
        'tanggal_laporan',
        'keterangan',
        'file_laporan',
        'lampiran_tambahan',
        'uploaded_by',
    ];

    protected $casts = [
        'tanggal_laporan' => 'date',
        'lampiran_tambahan' => 'array',
    ];

    public function proyek()
    {
        return $this->belongsTo(Proyek::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
