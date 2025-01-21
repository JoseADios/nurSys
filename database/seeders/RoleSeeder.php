<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::create([
            'name' => 'admin'
        ]);
        Role::create([
            'name' => 'doctor'
        ]);
        Role::create([
            'name' => 'recepcionist'
        ]);
        Role::create([
            'name' => 'nurse'
        ]);

        $role = Role::findByName('admin');
        $permissions = Permission::all();
        $role->syncPermissions($permissions);

        $role = Role::findByName('doctor');
        $role->syncPermissions([
            'view-admission',
            'view-medicalOrder',
            'create-medicalOrder'
        ]);

        $role = Role::findByName('recepcionist');
        $role->syncPermissions([
            'view-admission',
            'edit-admission',
            'create-admission',
            'delete-admission',
        ]);

        $role = Role::findByName('nurse');
        $role->syncPermissions([
            'view-admission',
        ]);



        $user = User::where('name', 'Test User')->first();
        $user->assignRole('admin');
    }
}
