<?php

namespace App\Policies;

use App\Models\TemperatureDetail;
use App\Models\TemperatureRecord;
use App\Models\User;
use App\Services\TurnService;
use Illuminate\Auth\Access\Response;

class TemperatureDetailPolicy
{
    /**
     * Perform pre-authorization checks.
     */
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
            return Response::deny('No se pueden crear registros en un ingreso que ha sido dado de alta.');
        }

        if ($this->hasTemperatureInCurrentTurn($temperature_record_id)) {
            return Response::deny('Ya hay una temperatura creada en el mismo turno.');
        }

        return Response::deny();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, TemperatureDetail $temperatureDetail): Response
    {
        $temperatureRecord = $temperatureDetail->temperatureRecord;

        if ($temperatureRecord->admission->discharged_date !== null) {
            return Response::deny('No se pueden crear registros en un ingreso que ha sido dado de alta.');
        }

        if (!($temperatureDetail->nurse_id == $user->id && $this->isInCurrentTurn($temperatureDetail))) {
            return Response::deny('No tienes permiso para actualizar este registro de enfermería.');
        }

        return Response::allow();

    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, TemperatureDetail $temperatureDetail): bool
    {
        return false;
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


    /**
     * Check if the temperature detail is in the current turn.
     */
    private function isInCurrentTurn(TemperatureDetail $temperatureDetail): bool
    {
        $turnService = new TurnService();
        $currentTurn = $turnService->getCurrentTurn();
        $dateRange = $turnService->getDateRangeForTurn($currentTurn);

        return $temperatureDetail->created_at->between($dateRange['start'], $dateRange['end']);
    }

    /**
     * Check if the temperature record has a temperature in the current turn.
     */
    private function hasTemperatureInCurrentTurn($temperature_record_id): bool
    {
        $turnService = new TurnService();
        $currentTurn = $turnService->getCurrentTurn();
        $dateRange = $turnService->getDateRangeForTurn($currentTurn);

        $lastTemperatureDetail = TemperatureDetail::where('temperature_record_id', $temperature_record_id)
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->orderBy('created_at', 'asc')
            ->first();

        return $lastTemperatureDetail !== null;
    }
}
