<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\LogsActivity;

class BeritaAcaraKonsultan extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'proyek_id',
        'konsultan_id',
        'judul',
        'tanggal',
        'pembahasan',
        'file_berita_acara',
        'dokumentasi',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'dokumentasi' => 'array',
    ];

    public function proyek()
    {
        return $this->belongsTo(Proyek::class);
    }

    public function konsultan()
    {
        return $this->belongsTo(User::class, 'konsultan_id');
    }
}
