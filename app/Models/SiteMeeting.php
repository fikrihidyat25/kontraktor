<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteMeeting extends Model
{
    use HasFactory;

    protected $fillable = [
        'proyek_id',
        'created_by',
        'jenis_rapat',
        'pertemuan_ke',
        'tanggal_rapat',
        'agenda_rapat',
        'dokumen_berita_acara',
        'dokumentasi',
        'lampiran_tambahan',
    ];

    protected $casts = [
        'tanggal_rapat' => 'date',
        'dokumentasi' => 'array',
    ];

    public function proyek()
    {
        return $this->belongsTo(Proyek::class);
    }

    public function pembuat()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
