<?php

namespace App\Policies;

use App\Models\NurseRecord;
use App\Models\NurseRecordDetail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Log;

class NurseRecordDetailPolicy
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
    public function view(User $user, NurseRecordDetail $nurseRecordDetail): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, NurseRecord $nurseRecord): bool
    {
        return $user->hasRole('admin') || $nurseRecord->admission->discharged_date === null;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, NurseRecordDetail $nurseRecordDetail): bool
    {
        return $user->hasRole('admin') || $nurseRecordDetail->nurseRecord->admission->discharged_date === null;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, NurseRecordDetail $nurseRecordDetail): bool
    {
        return $user->hasRole('admin') || $nurseRecordDetail->nurseRecord->admission->discharged_date === null;
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
