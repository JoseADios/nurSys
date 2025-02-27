<?php

namespace App\Policies;

use App\Models\Admission;
use App\Models\NurseRecord;
use App\Models\User;
use App\Services\TurnService;
use Illuminate\Auth\Access\Response;
use Log;

class NurseRecordPolicy
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
    public function view(User $user, NurseRecord $nurseRecord): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, Admission $admission): Response
    {
        if ($admission->discharged_date === null) {
            Response::allow();
        }

        return Response::deny('No se pueden crear registros para un ingreso que ya ha sido dado de alta.');
    }

    /**
     * Determine whether the user can create a NurseRecord in the current turn.
     */
    public function canCreateInTurn(User $user, Admission $admission): Response
    {
        $turnService = new TurnService();
        $currentTurn = $turnService->getCurrentTurn();
        $dateRange = $turnService->getDateRangeForTurn($currentTurn);

        $nurseRecordsInTurn = NurseRecord::where('admission_id', $admission->id)
            ->where('active', true)
            ->whereBetween('created_at', [
                $dateRange['start'],
                $dateRange['end']
            ])
            ->first();

        if ($nurseRecordsInTurn === null) {
            Response::allow();
        }

        return Response::deny('Ya hay un registro de enfermería creado para el ingreso en el turno actual');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, NurseRecord $nurseRecord): Response
    {
        if ($nurseRecord->admission->discharged_date === null && $user->id === $nurseRecord->nurse_id) {
            return Response::allow();
        }

        return Response::deny('No tienes permiso para actualizar este registro de enfermería.');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, NurseRecord $nurseRecord): Response
    {
        if ($nurseRecord->admission->discharged_date === null) {
            Response::allow();
        }

        return Response::deny('No se pueden eliminar registros en un ingreso que ya ha sido dado de alta.');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, NurseRecord $nurseRecord): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, NurseRecord $nurseRecord): bool
    {
        return false;
    }
}
