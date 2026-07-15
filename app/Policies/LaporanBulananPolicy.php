<?php

namespace App\Policies;

use App\Models\LaporanBulanan;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class LaporanBulananPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['super_admin', 'ppk', 'kontraktor', 'konsultan']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, LaporanBulanan $laporanBulanan): bool
    {
        if ($user->isSuperAdmin()) return true;
        if ($user->isKontraktor()) return $user->id === $laporanBulanan->kontraktor_id;
        if ($user->isKonsultan()) {
            return $laporanBulanan->proyek->konsultan_id === $user->id;
        }
        if ($user->isPPK()) {
            return $laporanBulanan->proyek->ppk_id === $user->id;
        }
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isKontraktor();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, LaporanBulanan $laporanBulanan): bool
    {
        return $user->isKontraktor() && in_array($laporanBulanan->status, ['draft', 'rejected']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, LaporanBulanan $laporanBulanan): bool
    {
        return $user->isKontraktor() && $laporanBulanan->status === 'draft';
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, LaporanBulanan $laporanBulanan): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, LaporanBulanan $laporanBulanan): bool
    {
        return false;
    }

    public function submit(User $user, LaporanBulanan $laporanBulanan): bool
    {
        return $user->isKontraktor() && in_array($laporanBulanan->status, ['draft', 'rejected']) && $user->id === $laporanBulanan->kontraktor_id;
    }

    public function verify(User $user, LaporanBulanan $laporanBulanan): bool
    {
        return $user->isKonsultan() && $laporanBulanan->status === 'submitted' && $laporanBulanan->proyek->konsultan_id === $user->id;
    }

    public function approve(User $user, LaporanBulanan $laporanBulanan): bool
    {
        return $user->isPPK() && $laporanBulanan->status === 'verified' && $laporanBulanan->proyek->ppk_id === $user->id;
    }
}
