<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\LogsActivity;

class LaporanPengawas extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'proyek_id',
        'konsultan_id',
        'jenis',
        'file_laporan',
        'catatan',
        'status',
        'verified_by',
        'verified_at',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
    ];

    public function proyek()
    {
        return $this->belongsTo(Proyek::class);
    }

    public function konsultan()
    {
        return $this->belongsTo(User::class, 'konsultan_id');
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
