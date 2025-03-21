<?php

namespace App\Policies;
use Illuminate\Database\Eloquent\Builder;
use App\Models\MedicalOrder;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use App\Models\Admission;
class MedicalOrderPolicy
{
    /**
     * Determine whether the user can view any models.
     */

    public function before(User $user, string $ability): bool|null
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        return null;
    }
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
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

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, MedicalOrder $medicalOrder): bool
    {
        return false;
    }
    public function updateAdmission(User $user, $new_admission_id): Response
    {
        // Validar que la nueva admisión no tenga otra hoja de temperatura activa
        $newAdmission = Admission::where('id', $new_admission_id)
            ->whereDoesntHave('medicalOrders', function (Builder $query) {
                $query->where('active', true);
            })
            ->first();

        if ($newAdmission) {
            return Response::allow();
        }

        return Response::deny('El nuevo ingreso ya tiene una Orden Medica activa.');
    }
}
