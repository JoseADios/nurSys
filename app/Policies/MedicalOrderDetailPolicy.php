<?php

namespace App\Policies;

use App\Models\MedicalOrder;
use App\Models\MedicalOrderDetail;
use App\Models\MedicationRecordDetail;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Log;


class MedicalOrderDetailPolicy
{
    public function before(User $user, string $ability): bool|null
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        return null;
    }

    public function view(User $user, MedicalOrderDetail $medicalOrderDetail): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, MedicalOrder $medicalOrder): Response
    {
        // padre
        $recordPolicy = new MedicalOrderPolicy();
        $responseRecord = $recordPolicy->update($user, $medicalOrder);

        if (!$responseRecord->allowed()) {
            return Response::deny($responseRecord->message());
        }
        if ($medicalOrder->admission->doctor_id !== $user->id) {
            return Response::deny('No tienes permiso para crear este registro');
        }

        return Response::allow();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, MedicalOrderDetail $medicalOrderDetail): Response
    {
        $recordPolicy = new MedicalOrderPolicy();
        $responseRecord = $recordPolicy->update($user, $medicalOrderDetail->medicalOrder);

        if (!$responseRecord->allowed()) {
            return Response::deny($responseRecord->message());
        }
        if ($medicalOrderDetail->medicalOrder->doctor_id !== $user->id) {
            return Response::deny('No tienes permiso para crear este registro');
        }
        return Response::allow();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, MedicalOrderDetail $medicalOrderDetail): Response
    {
        $recordPolicy = new MedicalOrderPolicy();
        $responseRecord = $recordPolicy->update($user, $medicalOrderDetail->medicalOrder);

        $medicationRecordDetail = MedicationRecordDetail::where('medical_order_detail_id', $medicalOrderDetail->id)
        ->first();

        if ($medicationRecordDetail) {
            return Response::deny('No se puede eliminar porque tiene medicamentos relacionados en la ficha, solo puedes suspender la orden');
        }

        if (!$responseRecord->allowed()) {
            return Response::deny($responseRecord->message());
        }
        if ($medicalOrderDetail->medicalOrder->doctor_id !== $user->id) {
            return Response::deny('No tienes permiso para eliminar este registro');
        }
        return Response::allow();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, MedicalOrderDetail $medicalOrderDetail): bool
    {
        return false;
    }

}
