<?php

namespace App\Policies;

use App\Models\NurseRecord;
use App\Models\NurseRecordDetail;
use App\Models\User;
use App\Services\TurnService;
use Gate;
use Illuminate\Auth\Access\Response;

class NurseRecordDetailPolicy
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
    public function view(User $user, NurseRecordDetail $nurseRecordDetail): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, NurseRecord $nurseRecord): Response
    {
        if (!$user->hasRole('nurse')) {
            return Response::deny('No tienes permiso para crear registros');
        }
        if ($nurseRecord->nurse_id !== $user->id) {
            return Response::deny('No tienes permiso para actualizar este registro');
        }
        if (!$nurseRecord->active) {
            return Response::deny('No se puede actualizar un registro eliminado');
        }
        if ($nurseRecord->admission->discharged_date !== null) {
            return Response::deny('No se pueden crear registros para un ingreso que ya ha sido dado de alta');
        }
        if ($this->canCreateInTurn($user, $nurseRecord)->denied()) {
            return Response::deny('No puedes crear registros en turnos pasados');
        }

        return Response::allow();
    }

    // verificar si el record fue creado en un turno pasado
    public function canCreateInTurn(User $user, NurseRecord $nurseRecord): Response
    {
        $turnService = new TurnService();
        $currentTurn = $turnService->getCurrentTurn();
        $dateRange = $turnService->getDateRangeForTurn($currentTurn);

        if ($nurseRecord->created_at < $dateRange['start']) {
            return Response::deny('No puedes crear registros en turnos pasados');
        }

        return Response::allow();
    }


    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, NurseRecordDetail $nurseRecordDetail): Response
    {
        // verificar que el user pueda actualizar el padre
        return Gate::inspect('update', $nurseRecordDetail->nurseRecord);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, NurseRecordDetail $nurseRecordDetail): Response
    {
        return Gate::inspect('delete', $nurseRecordDetail->nurseRecord);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, NurseRecordDetail $nurseRecordDetail): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, NurseRecordDetail $nurseRecordDetail): bool
    {
        return false;
    }
}
