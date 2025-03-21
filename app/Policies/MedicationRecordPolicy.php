<?php

namespace App\Policies;

use App\Models\MedicationRecord;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class MedicationRecordPolicy
{  public function before(User $user, string $ability): bool|null
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
       if ($user->hasRole('nurse') || $user->hasRole('doctor')) {
        return Response::allow();

       }
       return Response::deny('Ya hay un registro de enfermería creado para este ingreso en el turno actual - Enferemero: ' . $nurseRecordInTurn->nurse->name . ' ' . $nurseRecordInTurn->nurse->last_name);

    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        if ($user->hasRole('nurse') ) {
            return Response::allow();

           }
           return Response::deny('Ya hay un registro de enfermería creado para este ingreso en el turno actual - Enferemero: ' . $nurseRecordInTurn->nurse->name . ' ' . $nurseRecordInTurn->nurse->last_name);

    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, MedicationRecord $medicationRecord): bool
    {
        if ($user->hasRole('nurse') ) {
            return Response::allow();

           }
           return Response::deny('Ya hay un registro de enfermería creado para este ingreso en el turno actual - Enferemero: ' . $nurseRecordInTurn->nurse->name . ' ' . $nurseRecordInTurn->nurse->last_name);

    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, MedicationRecord $medicationRecord): bool
    {
        if ($user->hasRole('nurse') ) {
            return Response::allow();

           }
           return Response::deny('Ya hay un registro de enfermería creado para este ingreso en el turno actual - Enferemero: ' . $nurseRecordInTurn->nurse->name . ' ' . $nurseRecordInTurn->nurse->last_name);

    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, MedicationRecord $medicationRecord): bool
    {
        if ($user->hasRole('nurse') ) {
            return Response::allow();

           }
           return Response::deny('Ya hay un registro de enfermería creado para este ingreso en el turno actual - Enferemero: ' . $nurseRecordInTurn->nurse->name . ' ' . $nurseRecordInTurn->nurse->last_name);

    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, MedicationRecord $medicationRecord): bool
    {
        return false;
    }
}
