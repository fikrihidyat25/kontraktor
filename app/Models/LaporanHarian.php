<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LaporanHarian extends Model
{
    use HasFactory;

    protected $fillable = [
        'proyek_id',
        'kontraktor_id',
        'tanggal',
        'kondisi_cuaca',
        'catatan',
        'status',
        'catatan_konsultan',
        'verified_by',
        'verified_at',
        'catatan_ppk',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'tanggal'     => 'date',
        'verified_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    public function proyek()
    {
        return $this->belongsTo(Proyek::class);
    }

    public function kontraktor()
    {
        return $this->belongsTo(User::class, 'kontraktor_id');
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function tenagaKerjas()
    {
        return $this->hasMany(TenagaKerja::class);
    }

    public function materials()
    {
        return $this->hasMany(Material::class);
    }

    public function peralatans()
    {
        return $this->hasMany(Peralatan::class);
    }

    public function realisasiBiayas()
    {
        return $this->hasMany(RealisasiBiaya::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'draft'     => 'Draft',
            'submitted' => 'Menunggu Verifikasi',
            'verified'  => 'Terverifikasi',
            'approved'  => 'Disetujui',
            'rejected'  => 'Ditolak',
            default     => ucfirst($this->status),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'draft'     => 'text-gray-500 bg-gray-100',
            'submitted' => 'text-amber-700 bg-amber-100',
            'verified'  => 'text-blue-700 bg-blue-100',
            'approved'  => 'text-green-700 bg-green-100',
            'rejected'  => 'text-red-700 bg-red-100',
            default     => 'text-gray-500 bg-gray-100',
        };
    }

    public function getCuacaLabelAttribute(): string
    {
        return match($this->kondisi_cuaca) {
            'cerah'        => '☀️ Cerah',
            'berawan'      => '⛅ Berawan',
            'hujan_ringan' => '🌦️ Hujan Ringan',
            'hujan_lebat'  => '🌧️ Hujan Lebat',
            default        => ucfirst($this->kondisi_cuaca),
        };
    }
}
