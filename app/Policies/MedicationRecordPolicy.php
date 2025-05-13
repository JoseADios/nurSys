<?php

namespace App\Policies;

use App\Models\Admission;
use App\Models\MedicationRecord;
use App\Models\MedicationRecordDetail;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Auth;

class MedicationRecordPolicy
{
    // crear: enfermera, ingreso no dado de alta,
    // actualizar: ...
    // eliminar que no tenga detalles
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
    public function view(User $user, MedicationRecord $medicationRecord): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, $admission_id): Response
    {
        if (!$user->hasRole('nurse')) {
            return Response::deny('No tienes el rol necesario para crear fichas de medicamentos');
        }

        $admission = Admission::find($admission_id);

        if ($admission->discharged_date !== null) {
            return Response::deny('No se pueden actualizar registros en un ingreso que ya ha sido dado de alta');
        }

        return Response::allow();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, MedicationRecord $medicationRecord): Response
    {
        if (!$user->hasRole('nurse')) {
            return Response::deny('No tienes el rol necesario para actualizar este registro');
        }

        $admission = $medicationRecord->admission;

        if ($admission->discharged_date !== null) {
            return Response::deny('No se pueden actualizar registros en un ingreso que ya ha sido dado de alta');
        }

        return Response::allow();
    }
    public function updateNurse(User $user): Response
    {
        return Response::deny('No tienes permiso para actualizar esta información');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, MedicationRecord $medicationRecord): Response
    {
        if (!$user->hasRole('nurse')) {
            return Response::deny('No tienes el rol necesario para crear ordenes medicas');
        }

        $admission = $medicationRecord->admission;

        if ($admission->discharged_date !== null) {
            return Response::deny('No se pueden actualizar registros en un ingreso que ya ha sido dado de alta');
        }

        // verificar que no tenga detalles activos
        $details = MedicationRecordDetail::where('medication_record_id', $medicationRecord->id)->get();

        if (!$details->isEmpty()) {
            return Response::deny('No se pueden eliminar registros con medicamentos registrados');
        }

        return Response::allow();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, MedicationRecord $medicationRecord): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, MedicationRecord $medicationRecord): bool
    {
        return false;
    }
}
