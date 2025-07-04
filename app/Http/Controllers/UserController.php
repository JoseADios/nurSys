<?php

namespace App\Http\Controllers;

use App\Actions\Fortify\PasswordValidationRules;
use App\Models\ClinicArea;
use App\Models\User;
use Auth;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Log;
use Spatie\Permission\Models\Role;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class UserController extends Controller implements HasMiddleware
{

    public static function middleware(): array
    {
        return [
            new Middleware('permission:user.view', only: ['index', 'show']),
            new Middleware('permission:user.create', only: ['create', 'store']),
            new Middleware('permission:user.update', only: ['edit', 'update']),
            new Middleware('permission:user.delete', only: ['destroy']),
        ];
    }

    use PasswordValidationRules;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $role = $request->input('role');
        $specialty = $request->input('specialty');
        $position = $request->input('position');
        $email = $request->input('email');
        $show_deleted = $request->boolean('show_deleted');
        $sortField = $request->input('sortField');
        $sortDirection = $request->input('sortDirection', 'asc');

        $query = User::query();

        if ($show_deleted) {
            $query->where('active', false);
        } else {
            $query->where('active', true);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereRaw("CONCAT(name, ' ', last_name) like ?", ['%' . $search . '%']);
            });
        }

        if ($role) {
            $query->whereHas('roles', function ($q) use ($role) {
                $q->where('name', 'like', '%' . $role . '%');
            });
        }
        if ($specialty) {
            $query->where('specialty', 'like', '%' . $specialty . '%');
        }
        if ($position) {
            $query->where('position', 'like', '%' . $position . '%');
        }
        if ($email) {
            $query->where('email', 'like', '%' . $email . '%');
        }

        // Ordenamiento
        if ($sortField === 'role') {
            $query->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                ->orderBy('roles.name', $sortDirection)
                ->select('users.*');
        } elseif ($sortField) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->orderBy('updated_at', 'desc');
        }

        $users = $query->with('roles')->paginate(10);

        return Inertia::render('Users/Index', [
            'users' => $users,
            'filters' => [
                'search' => $search,
                'role' => $role,
                'specialty' => $specialty,
                'position' => $position,
                'email' => $email,
                'show_deleted' => $show_deleted,
                'sortField' => $sortField,
                'sortDirection' => $sortDirection,
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::orderBy('name', 'asc')->get();
        $areas = ClinicArea::all();
        return Inertia::render('Users/Create', [
            'roles' => $roles,
            'areas' => $areas,
            'previousUrl' => URL::previous(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'role' => ['required', 'string', 'max:255', Rule::exists('roles', 'name')],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => array_merge($this->passwordRules(), ['confirmed']),
            'password_confirmation' => ['required'],
            'identification_card' => ['required', 'size:12', 'unique:users'],
            'specialty' => ['required', 'string', 'max:255'],
            'area' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'size:14'],
            'address' => ['required', 'string', 'max:255'],
            'birthdate' => ['required', 'date', 'before:' . Carbon::now()->subYears(18)->format('Y-m-d')],
            'position' => ['nullable', 'string', 'max:255'],
            'comments' => ['string'],
        ])->sometimes('exequatur', ['required', 'string', 'max:255', 'unique:users'], function ($input) {
            return in_array($input->role, ['doctor', 'nurse']);
        })->validate();

        try {
            DB::beginTransaction();
            $user = User::create($validated);
            $user->syncRoles($request->role);
            DB::commit();
        } catch (Exception $e) {
            Log::error($e->getMessage());
            DB::rollBack();
        }

        if ($request['saveAndNew'] == True) {
            return Redirect::route('users.create')
                ->with('flash.toast', 'Usuario creado correctamente');
        }
        return Redirect::route('users.show', $user->id)->with('flash.toast', 'Usuario creado correctamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $user->load('roles');
        return Inertia::render('Users/Show', [
            'user' => $user,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $hasRoles = $user->getRoleNames();
        $areas = ClinicArea::all();
        $roles = Role::orderBy('name', 'asc')->get();
        return Inertia::render('Users/Edit', [
            'user' => $user,
            'areas' => $areas,
            'roles' => $roles,
            'hasRoles' => $hasRoles,
            'previousUrl' => URL::previous(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {

        if ($request->has('active')) {
            $validated = Validator::make($request->all(), [
                'active' => 'nullable|boolean',
            ])->validate();
        } else {
            $validated = Validator::make($request->all(), [
                'name' => ['required', 'string', 'max:255'],
                'last_name' => ['required', 'string', 'max:255'],
                'role' => ['required', 'string', 'max:255', Rule::exists('roles', 'name')],
                'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
                'identification_card' => ['required', 'size:12', Rule::unique('users')->ignore($user->id)],
                'specialty' => ['required', 'string', 'max:255'],
                'area' => ['required', 'string', 'max:255'],
                'phone' => ['required', 'size:14'],
                'address' => ['required', 'string', 'max:255'],
                'birthdate' => ['required', 'date', 'before:' . Carbon::now()->subYears(18)->format('Y-m-d')],
                'position' => ['nullable', 'string', 'max:255'],
                'comments' => ['string'],
                'active' => ['nullable', 'boolean'],
            ])->sometimes('exequatur', ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)], function ($input) {
                return in_array($input->role, ['doctor', 'nurse']);
            })->validate();
        }

        $user->update($validated);

        if ($request->has('role') && !$user->hasRole($validated['role'])) {
            try {
                $user->updateRole($request->role);
            } catch (Exception $e) {
                return redirect()->back()->with('flash.toast', $e->getMessage())->with('flash.toastStyle', 'danger');
            }
        }

        return back()->with('flash.toast', 'Perfil de usuario actualizado correctamente');
    }

    public function updatePassword(Request $request, User $user)
    {
        $validated = Validator::make($request->all(), [
            'password' => $this->passwordRules(),
        ])->validate();

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('flash.toast', 'Contraseña actualizada correctamente');
    }

    public function toggleActive(Request $request, User $user)
    {
        $validated = Validator::make($request->all(), [
            'active' => 'boolean'
        ])->validate();

        $user->update($validated);

        return back()->with('flash.toast', 'Estado del usuario actualizado correctamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //validar que el usuario no sea protegido
        if (in_array($user->id, [1, 2])) {
            return back()->with('flash.toast', 'No se puede desactivar este usuario porque está protegido.')->with('flash.toastStyle', 'danger');
        }

        $user->update(['active' => false]);
        DB::table('sessions')->where('user_id', $user->id)->delete();
        return back()->with('flash.toast', 'Usuario desactivado y sesión cerrada.');
    }

    public function filterUsers(Request $request)
    {
        $selectedUser = null;
        $user_id = $request->input('user_id');

        $query = User::query()
            ->where('active', true);

        if ($request->filled('filters.name')) {
            $query->whereRaw("CONCAT(name, ' ', last_name) like ?", ['%' . $request->input('filters.name') . '%']);
        }

        $roles = $request->input('filters.roles');

        if ($roles && !Auth::user()->hasRole('admin')) {
            $query->whereHas('roles', function ($q) use ($roles) {
                $q->whereIn('name', (array) $roles);
            });
        }

        $query->with('roles');

        if ($user_id) {
            $selectedUser = User::with('roles')->find($user_id);
        }

        $users = $query->paginate(10);

        if ($selectedUser && !$users->contains('id', $selectedUser->id)) {
            $usersCollection = $users->getCollection();
            $usersCollection->add($selectedUser);
            $sortedUsers = $usersCollection->sortByDesc('id')->values();
            $users->setCollection($sortedUsers);
        }

        return $users;
    }
}
