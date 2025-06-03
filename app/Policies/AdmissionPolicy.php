<?php

namespace App\Policies;

use App\Models\Admission;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AdmissionPolicy
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
    public function view(User $user, Admission $admission): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): Response
    {
        if (!$user->hasPermissionTo('admission.create')) {
            return Response::deny('El usuario no tiene los permisos necesarios para crear ingresos');
        }

        return Response::allow();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Admission $admission): Response
    {
        if (!$user->hasPermissionTo('admission.update')) {
            return Response::deny('El usuario no tiene los permisos necesarios para crear ingresos');
        }

        if (!$user->hasRole('doctor')) {
            return Response::deny('No tienes el rol necesario para modificar este registro');
        }

        if ($user->id !== $admission->doctor_id) {
            return Response::deny('No eres el doctor asignado a este ingreso, no puedes modificarlo');
        }

        if ($admission->discharged_date) {
            return Response::deny('No se puede modificar un ingreso dado de alta');
        }

        return Response::allow();
    }
    public function updateReceptionist(User $user): Response
    {
        return Response::deny('No tienes permiso para actualizar esta información');
    }
    public function setBed(User $user, Admission $admission): Response
    {
        if (!$user->hasRole('nurse')) {
            return Response::deny('No puedes tienes permiso para modificar la cama');
        }
        if ($admission->discharged_date) {
            return Response::deny('No se puede modificar la cama de un ingreso dado de alta');
        }

        return Response::allow();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Admission $admission): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Admission $admission): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Admission $admission): bool
    {
        return false;
    }
}
