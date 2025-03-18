<?php

namespace App\Policies;

use App\Models\User;

class MedicalOrderDetailPolicy
{
    public function before(User $user, string $ability): bool|null
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        return null;
    }
    public function view(User $user, MedicalOrder $medicalOrder): bool
    {

        if ($user->hasRole('doctor') || $user->hasRole('nurse')) {
            return Response::allow();
        }
        return Response::deny('No tienes permiso para ver este registro');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {

        if ($user->hasRole('doctor')) {
            return Response::allow();
        }
        return Response::deny('No tienes permiso para crear este registro');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, MedicalOrder $medicalOrder): bool
    {
        if ($user->hasRole('doctor')) {
            return Response::allow();
        }
        return Response::deny('No tienes permiso para actualizar este registro');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, MedicalOrder $medicalOrder): bool
    {
        if ($user->hasRole('doctor')) {
            return Response::allow();
        }
        return Response::deny('No tienes permiso para eliminar este registro');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, MedicalOrder $medicalOrder): bool
    {
        if ($user->hasRole('doctor')) {
            return Response::allow();
        }
        return Response::deny('No tienes permiso para restaurar este registro');
    }

}
