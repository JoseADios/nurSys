<?php

namespace App\Policies;

use App\Models\MedicalOrderDetail;
use App\Models\User;
use Response;

class MedicalOrderDetailPolicy
{
    public function before(User $user, string $ability): bool|null
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        return null;
    }

    public function view(User $user, MedicalOrderDetail $medicalOrder): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, $medicalOrder): Response
    {
        // padre
        $recordPolicy = new MedicalOrderPolicy();
        $responseRecord = $recordPolicy->update($user, $medicalOrder->admission->id);

        if (!$responseRecord->allowed()) {
            return Response::deny($responseRecord->message());
        }
        if (!$medicalOrder->doctor_id !== $user->id) {
            return Response::deny('No tienes permiso para crear este registro');
        }

        return Response::allow();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, MedicalOrderDetail $medicalOrder): bool
    {
        $recordPolicy = new MedicalOrderPolicy();
        $responseRecord = $recordPolicy->update($user, $medicalOrder->admission->id);

        if (!$responseRecord->allowed()) {
            return Response::deny($responseRecord->message());
        }
        if (!$medicalOrder->doctor_id !== $user->id) {
            return Response::deny('No tienes permiso para crear este registro');
        }
        return Response::allow();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, MedicalOrderDetail $medicalOrder): bool
    {
        $recordPolicy = new MedicalOrderPolicy();
        $responseRecord = $recordPolicy->update($user, $medicalOrder->admission->id);

        if (!$responseRecord->allowed()) {
            return Response::deny($responseRecord->message());
        }
        if (!$medicalOrder->doctor_id !== $user->id) {
            return Response::deny('No tienes permiso para crear este registro');
        }
        return Response::allow();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, MedicalOrderDetail $medicalOrder): bool
    {
        return false;
    }

}
