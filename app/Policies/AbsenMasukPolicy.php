<?php

namespace App\Policies;

use App\Models\AbsenMasuk;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AbsenMasukPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->id_roles, [1, 2]); // Admin (1) dan User (2) bisa melihat semua data
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, AbsenMasuk $absenMasuk): bool
    {
        return in_array($user->id_roles, [1, 2]); // Admin dan User bisa melihat satu data
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false; // Tidak ada yang bisa membuat absen secara manual
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, AbsenMasuk $absenMasuk): bool
    {
        return false; // Tidak ada yang bisa mengupdate absen
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, AbsenMasuk $absenMasuk): bool
    {
        return $user->id_roles == 1; // Hanya Admin (1) yang bisa menghapus data absen
    }

    /**
     * Determine whether the user can delete multiple models.
     */
    public function deleteAny(User $user): bool
    {
        return $user->id_roles == 1; // Hanya Admin (1) yang bisa menghapus banyak data
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, AbsenMasuk $absenMasuk): bool
    {
        return $user->id_roles == 1; // Hanya Admin yang bisa mengembalikan data absen
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, AbsenMasuk $absenMasuk): bool
    {
        return $user->id_roles == 1; // Hanya Admin yang bisa menghapus permanen data absen
    }
}
