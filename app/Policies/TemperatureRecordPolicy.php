<?php

namespace App\Policies;

use App\Models\Admission;
use App\Models\TemperatureRecord;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Database\Eloquent\Builder;
use Log;

class TemperatureRecordPolicy
{
    /**
     * Perform pre-authorization checks.
     */
    public function before(User $user, string $ability): bool|null
    {
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
    public function view(User $user, TemperatureRecord $temperatureRecord): Response
    {
        if ($user->hasRole('admin')) {
            return Response::allow();
        }
        if (!$temperatureRecord->active) {
            return Response::deny('No tienes permiso para ver este registro');
        }

        return Response::allow();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, $admission_id): Response
    {
        $admission = Admission::where('id',$admission_id)->first();

        if (!$admission->active) {
            return Response::deny('No se pueden crear registros en un ingreso desactivado');
        }

        if ($admission->discharged_date !== null) {
            return Response::deny('No se pueden crear registros en un ingreso que ha sido dado de alta');
        }

        $existingRecord = TemperatureRecord::where('admission_id', $admission_id)
            ->where('active', 1)
            ->first();

        if ($existingRecord) {
            return Response::deny('Ya existe una hoja de temperatura para este ingreso');
        }

        return Response::allow();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, TemperatureRecord $temperatureRecord): Response
    {
        if ($user->hasRole('admin')) {
            return Response::allow();
        }
        if (!$temperatureRecord->admission->active) {
            return Response::deny('No se pueden crear registros en un ingreso desactivado');
        }

        if ($temperatureRecord->admission->discharged_date !== null) {
            return Response::deny('No se pueden modificar registros en un ingreso que ha sido dado de alta');
        }

        return Response::allow();
    }

    /**
     * Determine whether the user can update the admission id in model.
     */
    public function updateAdmission(User $user, $new_admission_id): Response
    {
        if (!$user->hasRole('admin')) {
            return Response::deny();
        }

        // Validar que la nueva admisión no tenga otra hoja de temperatura activa
        $newAdmission = Admission::where('id', $new_admission_id)
            ->whereDoesntHave('TemperatureRecord', function (Builder $query) {
                $query->where('active', true);
            })
            ->first();

        if (!$newAdmission) {
            return Response::deny('El nuevo ingreso ya tiene una hoja de temperatura activa.');
        }

        return Response::allow();
    }

    /**
     * Determine whether the user can update the signature.
     */
    public function updateSignature(User $user, TemperatureRecord $temperatureRecord): Response
    {
        if ($user->hasRole('admin')) {
            return Response::allow();
        }

        if ($temperatureRecord->admission->discharged_date !== null) {
            return Response::deny('No se pueden modificar registros en un ingreso que ha sido dado de alta');
        }
        if (!$temperatureRecord->admission->active) {
            return Response::deny('No se pueden crear registros en un ingreso desactivado');
        }
        if (!$temperatureRecord->active) {
            return Response::deny('No se pueden modificar registros eliminados');
        }
        if ($temperatureRecord->nurse_id !== $user->id) {
            return Response::deny('No tienes permiso para firmar este registro');
        }

        return Response::allow();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, TemperatureRecord $temperatureRecord): Response
    {
        if (!$user->hasRole('admin')) {
            return Response::deny('No tienes permiso para eliminar este registro');
        }

        return Response::allow();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, TemperatureRecord $temperatureRecord): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, TemperatureRecord $temperatureRecord): bool
    {
        return false;
    }
}
