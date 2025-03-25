<?php

namespace App\Policies;

use App\Models\MedicationRecord;
use App\Models\User;
use Gate;
use Illuminate\Auth\Access\Response;
use App\Models\MedicationRecordDetail;
class MedicationRecordDetailPolicy
{

    // crear:

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
    public function view(User $user, medicationRecordDetail $medicationRecordDetail): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, medicationRecord $medicationRecord): Response
    {
        $recordPolicy = new MedicationRecordPolicy();
        return $recordPolicy->update($user, $medicationRecord);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, medicationRecordDetail $medicationRecordDetail): Response
    {
        // permisos del record
        $recordPolicy = new MedicationRecordPolicy();

        if ($recordPolicy->update($user, $medicationRecordDetail->medicationRecord)) {
            return Response::deny('No se puede actualizar este registro, ya se ha iniciado su aplicación al paciente');
        }

        // Verificar que ninguna notificación tenga el campo 'applied' en true
        $notifications = $medicationRecordDetail->medicationNotification;
        if ($notifications->contains('applied', true)) {
            return Response::deny('No se puede actualizar este registro, ya se ha iniciado su aplicación al paciente');
        }

        return Response::allow();
    }


    public function suspend(User $user, medicationRecordDetail $medicationRecordDetail): Response
    {
        $recordPolicy = new MedicationRecordPolicy();
        return $recordPolicy->update($user, $medicationRecordDetail->medicationRecord);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, medicationRecordDetail $medicationRecordDetail): Response
    {
        // permisos del record
        $recordPolicy = new MedicationRecordPolicy();

        $response = $recordPolicy->update($user, $medicationRecordDetail->medicationRecord);

        if (!$response->allowed()) {
            return Response::deny($response->message());
        }

        // Verificar que ninguna notificación tenga el campo 'applied' en true
        $notifications = $medicationRecordDetail->medicationNotification;
        if ($notifications->contains('applied', true)) {
            return Response::deny('No se puede eliminar este registro, ya se ha iniciado su aplicación al paciente');
        }

        return Response::allow();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, medicationRecordDetail $medicationRecordDetail): bool
    {

        if ($user->hasRole('nurse')) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, medicationRecordDetail $medicationRecordDetail): bool
    {
        return false;
    }
}
