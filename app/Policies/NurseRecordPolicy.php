<?php

namespace App\Policies;

use App\Models\Admission;
use App\Models\NurseRecord;
use App\Models\User;
use App\Services\TurnService;
use Illuminate\Auth\Access\Response;

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
    public function view(User $user, NurseRecord $nurseRecord): Response
    {
        if ($user->hasRole('doctor')) {
            return Response::allow();
        }
        if (!$nurseRecord->active && $nurseRecord->nurse_id !== $user->id) {
            return Response::deny('No tienes permiso para ver este registro');
        }

        return Response::allow();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, Admission $admission): Response
    {
        if (!$user->hasPermissionTo('nurseRecord.create')) {
            return Response::deny('El usuario no tiene los permisos necesarios para crear Registros de enfermería');
        }

        if (!$admission->active) {
            return Response::deny('No se pueden actualizar registros en un ingreso desactivado');
        }

        if ($admission->discharged_date !== null) {
            return Response::deny('No se pueden crear registros para un ingreso que ya ha sido dado de alta');
        }

        return Response::allow();
    }

    /**
     * Determine whether the user can create a NurseRecord in the current turn.
     */
    public function canCreateInTurn(User $user, int $admission_id): Response
    {
        $admission = Admission::find($admission_id);

        if (!$admission->active) {
            return Response::deny('No se pueden actualizar registros en un ingreso desactivado');
        }

        $turnService = new TurnService();
        $currentTurn = $turnService->getCurrentTurn();
        $dateRange = $turnService->getDateRangeForTurn($currentTurn);

        $nurseRecordInTurn = NurseRecord::where('admission_id', $admission_id)
            ->where('active', true)
            ->whereBetween('created_at', [
                $dateRange['start'],
                $dateRange['end']
            ])
            ->with('nurse')
            ->first();

        if ($nurseRecordInTurn !== null) {
            return Response::deny('Este ingreso ya tiene un registro de enfermería creado en el turno actual - Enfermero: ' . $nurseRecordInTurn->nurse->name . ' ' . $nurseRecordInTurn->nurse->last_name);
        }

        return Response::allow();
    }

    /**
     * Determine if recived date is in actual turn.
     */
    private function dateIsInActualTurn($date): bool
    {
        $turnService = new TurnService();
        $currentTurn = $turnService->getCurrentTurn();
        $dateRange = $turnService->getDateRangeForTurn($currentTurn);

        return $date >= $dateRange['start'] && $date < $dateRange['end'];
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, NurseRecord $nurseRecord): Response
    {
        if (!$this->dateIsInActualTurn($nurseRecord->created_at)) {
            return Response::deny('No se pueden actualizar registros de un turno pasado');
        }

        if (!$nurseRecord->admission->active) {
            return Response::deny('No se pueden actualizar registros en un ingreso desactivado');
        }

        if ($nurseRecord->admission->discharged_date !== null) {
            return Response::deny('No se pueden actualizar registros en un ingreso que ya ha sido dado de alta');
        }

        if ($user->id !== $nurseRecord->nurse_id) {
            return Response::deny('No tienes permiso para actualizar este registro de enfermería');
        }

        return Response::allow();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, NurseRecord $nurseRecord): Response
    {
        if (!$this->dateIsInActualTurn($nurseRecord->created_at)) {
            return Response::deny('No se pueden eliminar registros de un turno pasado');
        }

        if (!$nurseRecord->admission->active) {
            return Response::deny('No se pueden actualizar registros en un ingreso desactivado');
        }

        if ($nurseRecord->admission->discharged_date !== null) {
            return Response::deny('No se pueden eliminar registros en un ingreso que ya ha sido dado de alta.');
        }

        return Response::allow();
    }

    public function updateAdmission(User $user): Response
    {
        return Response::allow();
    }

    public function updateNurse(User $user): Response
    {
        return Response::deny('No tienes permiso para actualizar esta información');
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
