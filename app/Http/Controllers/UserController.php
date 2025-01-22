<?php

namespace App\Http\Controllers;

use App\Actions\Fortify\PasswordValidationRules;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    use PasswordValidationRules;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::all();
        $users->load('roles');
        return Inertia::render('Users/Index', [
            'users' => $users,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::orderBy('name', 'asc')->get();
        return Inertia::render('Users/Create', [
            'roles' => $roles,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => $this->passwordRules(),
            'identification_card' => ['required', 'string', 'max:255', 'unique:users'],
            'exequatur' => ['required', 'string', 'max:255', 'unique:users'],
            'specialty' => ['required', 'string', 'max:255'],
            'area' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'birthdate' => ['required', 'date'],
            'position' => ['required', 'string', 'max:255'],
            'comments' => ['string', 'max:255'],

        ])->validate();

        try {
            $user = User::create([
                'name' => $request['name'],
                'last_name' => $request['last_name'],
                'email' => $request['email'],
                'password' => Hash::make($request['password']),
                'identification_card' => $request['identification_card'],
                'exequatur' => $request['exequatur'],
                'specialty' => $request['specialty'],
                'area' => $request['area'],
                'phone' => $request['phone'],
                'address' => $request['address'],
                'birthdate' => $request['birthdate'],
                'position' => $request['position'],
                'comments' => $request['comments'],
            ]);

            $user->syncRoles($request->role);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
        }

        if ($request['saveAndNew'] == True) {
            return Redirect::route('users.create', [
                'reset' => true,
            ])->with('success', 'User created successfully.');
        } else {
            return Redirect::route('users.show', $user->id);
        }
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
        $roles = Role::orderBy('name', 'asc')->get();
        return Inertia::render('Users/Edit', [
            'user' => $user,
            'roles' => $roles,
            'hasRoles' => $hasRoles,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            // 'password' => $this->passwordRules(),
            'identification_card' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'exequatur' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'specialty' => ['required', 'string', 'max:255'],
            'area' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'birthdate' => ['required', 'date'],
            'position' => ['required', 'string', 'max:255'],
            'comments' => ['string', 'max:255'],

        ])->validate();

        $user->update([
            'name' => $request['name'],
            'last_name' => $request['last_name'],
            'email' => $request['email'],
            'identification_card' => $request['identification_card'],
            'exequatur' => $request['exequatur'],
            'specialty' => $request['specialty'],
            'area' => $request['area'],
            'phone' => $request['phone'],
            'address' => $request['address'],
            'birthdate' => $request['birthdate'],
            'position' => $request['position'],
            'comments' => $request['comments'],
        ]);

        $user->syncRoles($request->role);

        return Redirect::route('users.index', $user->id)->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
