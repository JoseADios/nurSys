<?php

namespace App\Policies;

use App\Models\TemperatureRecord;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TemperatureRecordPolicy
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
    public function view(User $user, TemperatureRecord $temperatureRecord): bool
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
    public function update(User $user, TemperatureRecord $temperatureRecord): bool
    {
        return $temperatureRecord->admission->discharged_date === null;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, TemperatureRecord $temperatureRecord): bool
    {
        return $temperatureRecord->admission->discharged_date === null;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, TemperatureRecord $temperatureRecord): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, TemperatureRecord $temperatureRecord): bool
    {
        return false;
    }
}
