<?php

namespace App\Policies;

use App\Models\MedicationNotification;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Database\Eloquent\Builder;
class MedicationNotificationPolicy
{
    public function before(User $user, string $ability): bool|null
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        return null;
    }
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }



    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, MedicationNotification $MedicationNotification): bool
    {
        if ($user->hasRole('nurse') || ($user->hasRole('doctor'))) {
            return Response::allow();
              }


              return Response::deny('No tienes permiso para Ver este registro');




    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, MedicationNotification $MedicationNotification): bool
    {
        if ($user->hasRole('nurse')) {
            return Response::allow();
        }
        return Response::deny('No tienes permiso para crear este registro');

    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, MedicationNotification $MedicationNotification): bool
    {
        if ($user->hasRole('nurse')) {
            return Response::allow();
        }
        return Response::deny('No tienes permiso para actualizar este registro');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, MedicationNotification $MedicationNotification): bool
    {

        if ($user->hasRole('nurse')) {
            return Response::allow();
        }
        return Response::deny('No tienes permiso para eliminar este registro');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, MedicationNotification $MedicationNotification): bool
    {

        if ($user->hasRole('nurse')) {
            return Response::allow();
        }
        return Response::deny('No tienes permiso para restaurar este registro');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, MedicationNotification $MedicationNotification): bool
    {
        return false;
    }
}
