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
            'name' => 'receptionist'
        ]);
        Role::create([
            'name' => 'nurse'
        ]);

        $role = Role::findByName('admin');
        $permissions = Permission::all();
        $role->syncPermissions($permissions);

        $role = Role::findByName('doctor');
        $role->syncPermissions([
            'admission.view',
            'medicalOrder.view',
            'medicalOrder.create',
            'temperatureRecord.view',
            'temperatureDetail.view',
        ]);

        $role = Role::findByName('receptionist');
        $role->syncPermissions([
            'admission.*',
        ]);

        $role = Role::findByName('nurse');
        $role->syncPermissions([
            'admission.view',
            'temperatureRecord.view',
            'temperatureRecord.create',
            'temperatureRecord.update',
            'temperatureDetail.*',
        ]);

        $user = User::where('name', 'Test User')->first();
        $user->assignRole('admin');

        // a los usuarios que estan creados en la base de datos, excepto test user asigna roles aleatorios
        $users = User::where('name', '!=', 'Test User')->get();
        $roles = Role::all()->pluck('name')->toArray();

        foreach ($users as $user) {
            $randomRole = $roles[array_rand($roles)];
            $user->assignRole($randomRole);
        }
    }
}
