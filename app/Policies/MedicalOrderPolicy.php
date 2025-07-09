<?php

namespace App\Policies;
use Illuminate\Database\Eloquent\Builder;
use App\Models\MedicalOrder;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use App\Models\Admission;
use Illuminate\Support\Facades\Auth;
use Log;
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
    public function view(User $user, MedicalOrder $medicalOrder): Response
    {
        if ($user->hasRole(['doctor', 'nurse'])) {
            return Response::allow();
        }

        return Response::allow();
    }

    /**
     * Determine whether the user can create models.
     */
    //  crear: doctores, en el ingreso solo crear el doctor relacionado,
    public function create(User $user, $admission_id): Response
    {
        if (!$user->hasRole('doctor')) {
            return Response::deny('No tienes el rol necesario para crear ordenes médicas');
        }

        $admission = Admission::findOrFail($admission_id);

        if ($admission->discharged_date !== null) {
            return Response::deny('No se pueden crear registros en un ingreso que ya ha sido dado de alta');
        }

        if ($admission->doctor_id !== Auth::id()) {
            return Response::deny('No tienes permiso para crear este registro');
        }

        $orderExistsToday  = MedicalOrder::where('admission_id', $admission_id)
            ->whereDate('created_at', today())
            ->exists();

        if ($orderExistsToday ) {
            return Response::deny('Ya existe una orden médica creada hoy para este ingreso. No se puede crear otra.');
        }

        return Response::allow();
    }
    public function updateAdmission(User $user): Response
    {
        return Response::allow();
    }
    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, MedicalOrder $medicalOrder): Response
    {
        if (!$user->hasPermissionTo('medicalOrder.update')) {
            return Response::deny('No tienes los permisos necesarios para crear ordenes medicas');
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
    public function updateNurse(User $user): Response
    {
        return Response::deny('No tienes permiso para actualizar esta información');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, MedicalOrder $medicalOrder): Response
    {
        if (!$user->hasRole('doctor')) {
            return Response::deny('No tienes el rol necesario para eliminar ordenes medicas');
        }

        $admission = $medicalOrder->admission;

        if ($admission->discharged_date !== null) {
            return Response::deny('No se pueden eliminar registros en un ingreso que ya ha sido dado de alta');
        }

        if ($admission->doctor_id !== Auth::id()) {
            return Response::deny('No tienes permiso para eliminar este registro');
        }

        if (!($medicalOrder->created_at >= now()->startOfDay() && $medicalOrder->created_at <= now()->endOfDay())) {
            return Response::deny('No se pueden eliminar registros de un dia pasado');
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
