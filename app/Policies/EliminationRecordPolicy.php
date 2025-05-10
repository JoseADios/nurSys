<?php

namespace App\Policies;

use App\Models\EliminationRecord;
use App\Models\TemperatureRecord;
use App\Models\User;
use App\Services\TurnService;
use Illuminate\Auth\Access\Response;
use Log;

class EliminationRecordPolicy
{
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
    public function view(User $user, EliminationRecord $eliminationRecord): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, $temperature_record_id): Response
    {
        $temperatureRecord = TemperatureRecord::where('id', $temperature_record_id)->firstOrFail();

        $temperatureRecordPolicy = new TemperatureRecordPolicy();
        $responseRecord = $temperatureRecordPolicy->update($user, $temperatureRecord);

        if (!$responseRecord->allowed()) {
            return Response::deny($responseRecord->message());
        }

        if ($this->hasEliminationInCurrentTurn($temperature_record_id)) {
            return Response::deny('Ya existe un registro de eliminaciones creado en este turno');
        }

        return Response::allow();
    }

    private function hasEliminationInCurrentTurn($temperature_record_id): bool
    {
        $turnService = new TurnService();
        $currentTurn = $turnService->getCurrentTurn();
        $dateRange = $turnService->getDateRangeForTurn($currentTurn);

        $lastEliminationRecord = EliminationRecord::where('temperature_record_id', $temperature_record_id)
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->orderBy('created_at', 'asc')
            ->first();

        Log::info($lastEliminationRecord);

        return $lastEliminationRecord !== null;
    }

    private function isInCurrentTurn(EliminationRecord $eliminationRecord): bool
    {
        $turnService = new TurnService();
        $currentTurn = $turnService->getCurrentTurn();
        $dateRange = $turnService->getDateRangeForTurn($currentTurn);

        return $eliminationRecord->created_at->between($dateRange['start'], $dateRange['end']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, EliminationRecord $eliminationRecord): Response
    {
        $temperatureRecord = $eliminationRecord->temperatureRecord;

        $temperatureRecordPolicy = new TemperatureRecordPolicy();
        $responseRecord = $temperatureRecordPolicy->update($user, $temperatureRecord);

        if (!$responseRecord->allowed()) {
            return Response::deny($responseRecord->message());
        }

        if (!$user->hasRole(['admin', 'nurse'])) {
            return Response::deny('No tienes el rol necesario para actualizar este registro');
        }

        if ($user->id !== $eliminationRecord->nurse_id) {
            return Response::deny('No tienes permiso para actualizar este registro');
        }
        // eliminations policy todo:
        if (!$this->isInCurrentTurn($eliminationRecord)) {
            return Response::deny('No se puede actualizar una temperatura de un turno pasado');
        }

        return Response::allow();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, EliminationRecord $eliminationRecord): Response
    {
        return Response::deny('No tienes permiso para eliminar este registro');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, EliminationRecord $eliminationRecord): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, EliminationRecord $eliminationRecord): bool
    {
        return false;
    }
}
