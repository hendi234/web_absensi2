<?php

namespace App\Policies;

use App\Models\AbsensiHarian;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AbsensiHarianPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, AbsensiHarian $absensiHarian): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, AbsensiHarian $absensiHarian): bool
    {
        return $user->id_roles == 1;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, AbsensiHarian $absensiHarian): bool
    {
        return $user->id_roles == 1;
    }

    public function deleteAny(User $user): bool
    {
        return $user->id_roles == 1;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, AbsensiHarian $absensiHarian): bool
    {
        return true;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, AbsensiHarian $absensiHarian): bool
    {
        return true;
    }
}
