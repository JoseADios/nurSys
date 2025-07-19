<?php

namespace App\Policies;

use App\Models\TemperatureDetail;
use App\Models\TemperatureRecord;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TemperatureDetailPolicy
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
    public function view(User $user, TemperatureDetail $temperatureDetail): bool
    {
        return false;
    }

    /**`
     * Determine whether the user can create models.
     */
    public function create(User $user, $temperature_record_id): Response
    {
        $temperatureRecord = TemperatureRecord::find($temperature_record_id);

        if ($temperatureRecord->admission->discharged_date !== null) {
            return Response::deny('No se pueden crear registros en un ingreso que ha sido dado de alta');
        }

        if (!$temperatureRecord->active) {
            return Response::deny('No se pueden modificar registros eliminados');
        }

        if (!$user->hasRole('admin') && !$user->hasRole('nurse')) {
            return Response::deny('No tienes permiso para crear registros de temperatura');
        }

        return Response::allow();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, TemperatureDetail $temperatureDetail): Response
    {
        $temperatureRecord = $temperatureDetail->temperatureRecord;

        if ($temperatureRecord->admission->discharged_date !== null) {
            return Response::deny('No se pueden crear registros en un ingreso que ha sido dado de alta');
        }

        if (!$temperatureRecord->active) {
            return Response::deny('No se pueden modificar registros eliminados');
        }

        if (!$user->hasRole('nurse')) {
            return Response::deny('No tienes permiso para actualizar registros de temperatura');
        }

        if ($temperatureDetail->nurse_id !== $user->id) {
            return Response::deny('No tienes permiso para actualizar este registro de temperatura');
        }


        return Response::allow();

    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, TemperatureDetail $temperatureDetail): Response
    {
        return Response::deny('No tienes permiso para eliminar registros de temperatura');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, TemperatureDetail $temperatureDetail): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, TemperatureDetail $temperatureDetail): bool
    {
        return false;
    }

}
