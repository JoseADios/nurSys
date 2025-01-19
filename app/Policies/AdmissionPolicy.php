<?php

namespace App\Policies;

use App\Models\Admission;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AdmissionPolicy
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
    public function view(User $user, Admission $admission): bool
    {
        if (!$admission->active) {
            return $user->hasRole(['admin', 'doctor']);
        }
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasRole(['admin', 'recepcionist']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Admission $admission): bool
    {
        if ($user->hasRole('doctor') && $admission->doctor_id == $user->id) {
            return true;
        }
        return $user->hasRole(['admin', 'nurse', 'recepcionist']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Admission $admission): bool
    {
        return $user->hasRole(['admin', 'recepcionist']);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Admission $admission): bool
    {
        return $user->hasRole(['admin', 'recepcionist']);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Admission $admission): bool
    {
        return false;
    }
}
