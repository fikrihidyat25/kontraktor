<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DokumenProyek extends Model
{
    use HasFactory;

    protected $fillable = [
        'proyek_id',
        'tipe_dokumen',
        'nama_dokumen',
        'file_path',
        'uploaded_by',
        'status',
        'catatan_validasi',
        'lampiran_tambahan',
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
