<?php

namespace App\Policies;
use Illuminate\Database\Eloquent\Builder;
use App\Models\MedicalOrder;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use App\Models\Admission;
use Illuminate\Support\Facades\Auth;
class MedicalOrderPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function before(User $user, string $ability): bool|null
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
    public function view(User $user, MedicalOrder $medicalOrder): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
      //  crear: doctores, en el ingreso solo crear el doctor relacionado,
    public function create(User $user, $admission_id): Response
    {
        if (!$user->hasRole('doctor')) {
            return Response::deny('No tienes el rol necesario para crear ordenes medicas');
        }

        $admission = Admission::find($admission_id);

        if ($admission->discharged_date !== null) {
            return Response::deny('No se pueden actualizar registros en un ingreso que ya ha sido dado de alta');
        }

        if ($admission->doctor_id !== Auth::id()) {
            return Response::deny('No tienes permiso para crear este registro');
        }

        return Response::allow();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, MedicalOrder $medicalOrder): Response
    {
        if (!$user->hasRole('doctor')) {
            return Response::deny('No tienes el rol necesario para crear ordenes medicas');
        }

        $admission = $medicalOrder->admission;

        if ($admission->discharged_date !== null) {
            return Response::deny('No se pueden actualizar registros en un ingreso que ya ha sido dado de alta');
        }

        if ($admission->doctor_id !== Auth::id()) {
            return Response::deny('No tienes permiso para crear este registro');
        }

        if (!($medicalOrder->created_at >= now()->startOfDay() && $medicalOrder->created_at <= now()->endOfDay())) {
            return Response::deny('No se pueden actualizar registros de un dia pasado');
        }

        return Response::allow();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, MedicalOrder $medicalOrder): Response
    {
        if (!$user->hasRole('doctor')) {
            return Response::deny('No tienes el rol necesario para crear ordenes medicas');
        }

        $admission = $medicalOrder->admission;

        if ($admission->discharged_date !== null) {
            return Response::deny('No se pueden actualizar registros en un ingreso que ya ha sido dado de alta');
        }

        if ($admission->doctor_id !== Auth::id()) {
            return Response::deny('No tienes permiso para crear este registro');
        }

        if (!($medicalOrder->created_at >= now()->startOfDay() && $medicalOrder->created_at <= now()->endOfDay())) {
            return Response::deny('No se pueden actualizar registros de un dia pasado');
        }

        return Response::allow();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, MedicalOrder $medicalOrder): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, MedicalOrder $medicalOrder): bool
    {
        return false;
    }
}
