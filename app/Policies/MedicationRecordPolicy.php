<?php

namespace App\Policies;

use App\Models\MedicationRecord;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class MedicationRecordPolicy
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
    public function view(User $user, MedicationRecord $medicationRecord): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, MedicationRecord $medicationRecord): bool
    {
        return $medicationRecord->admission->discharged_date === null;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, MedicationRecord $medicationRecord): bool
    {
        return $medicationRecord->admission->discharged_date === null;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, MedicationRecord $medicationRecord): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, MedicationRecord $medicationRecord): bool
    {
        return false;
    }
}
