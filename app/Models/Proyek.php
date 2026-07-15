<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Proyek extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_proyek',
        'nomor_kontrak',
        'lokasi',
        'nilai_kontrak',
        'tanggal_mulai',
        'tanggal_selesai',
        'kontraktor_id',
        'konsultan_id',
        'ppk_id',
        'status',
        'deskripsi',
    ];

    protected $casts = [
        'tanggal_mulai'   => 'date',
        'tanggal_selesai' => 'date',
        'nilai_kontrak'   => 'decimal:2',
    ];

    public function kontraktor()
    {
        return $this->belongsTo(User::class, 'kontraktor_id');
    }

    public function konsultan()
    {
        return $this->belongsTo(User::class, 'konsultan_id');
    }

    public function ppk()
    {
        return $this->belongsTo(User::class, 'ppk_id');
    }

    public function laporanHarians()
    {
        return $this->hasMany(LaporanHarian::class);
    }

    public function laporanMingguans()
    {
        return $this->hasMany(LaporanMingguan::class);
    }

    public function getDurasiHariAttribute(): int
    {
        return $this->tanggal_mulai->diffInDays($this->tanggal_selesai);
    }

    public function getProgressHariAttribute(): int
    {
        $start = $this->tanggal_mulai;
        $now   = now();
        if ($now->lt($start)) return 0;
        if ($now->gt($this->tanggal_selesai)) return $this->durasi_hari;
        return $start->diffInDays($now);
    }
}
