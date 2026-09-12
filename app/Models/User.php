<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'penanggung_jawab',
        'kab_kota',
        'skpd',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isKontraktor(): bool
    {
        return $this->role === 'kontraktor';
    }

    public function isKonsultan(): bool
    {
        return $this->role === 'konsultan';
    }

    public function isPPK(): bool
    {
        return $this->role === 'ppk';
    }

    public function isPPTK(): bool
    {
        return $this->role === 'pptk';
    }

    public function getRoleLabelAttribute(): string
    {
        return match($this->role) {
            'kontraktor'  => 'Kontraktor',
            'konsultan'   => 'Konsultan Pengawas',
            'ppk'         => 'PPK',
            'pptk'        => 'PPTK',
            default       => ucfirst($this->role),
        };
    }

    // Relationships
    public function proyekSebagaiKontraktor()
    {
        return $this->hasMany(Proyek::class, 'kontraktor_id');
    }

    public function proyekSebagaiKonsultan()
    {
        return $this->hasMany(Proyek::class, 'konsultan_id');
    }

    public function proyekSebagaiPPK()
    {
        return $this->hasMany(Proyek::class, 'ppk_id');
    }

    public function laporanHarians()
    {
        return $this->hasMany(LaporanHarian::class, 'kontraktor_id');
    }

    public function laporanMingguans()
    {
        return $this->hasMany(LaporanMingguan::class, 'kontraktor_id');
    }

    public function proyekSebagaiPPTK()
    {
        return $this->hasMany(Proyek::class, 'pptk_id');
    }

    public function dokumenProyeks()
    {
        return $this->hasMany(DokumenProyek::class, 'uploaded_by');
    }

    public function siteMeetings()
    {
        return $this->hasMany(SiteMeeting::class, 'konsultan_id');
    }
}
