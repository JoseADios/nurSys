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
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, MedicationNotification $MedicationNotification): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, MedicationNotification $medicationNotification): Response
    {
        $detail = $medicationNotification->medicationRecordDetail;
        $record = $detail->medicationRecord;
        $admission = $record->admission;

        if (!$user->hasPermissionTo('medicationNotification.update')) {
            return Response::deny('El usuario no tiene los permisos necesarios para actualizar notificaciones');
        }

        // validar que el ingreso este en progreso
        if ($admission->discharged_date) {
            return Response::deny('No se pueden actualizar registros de un ingreso dado de alta');
        }

        // validar que el ingreso este activo --- todo: poner para todos los policies
        if (!$admission->active) {
            return Response::deny('No se pueden actualizar registros de un ingreso desactivado');
        }

        // validar que el record este activo
        if (!$record->active) {
            return Response::deny('No se pueden actualizar registros de una ficha desactivada');
        }

        // validar que el detail este activo
        if (!$detail->active) {
            return Response::deny('No se pueden actualizar registros de una medicación desactivada');
        }
        // validar que el detail no este suspendido
        if ($detail->suspended_at) {
            return Response::deny('No se pueden actualizar registros de una medicación suspendida');
        }

        return Response::allow();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, MedicationNotification $MedicationNotification): Response
    {
        return Response::deny();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, MedicationNotification $MedicationNotification): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, MedicationNotification $MedicationNotification): bool
    {
        return false;
    }
}
