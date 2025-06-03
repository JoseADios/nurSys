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
            'admission.update',
            'temperatureRecord.view',
            'temperatureDetail.view',
            'patient.view',
            'patient.create',
            'patient.update',
            'nurseRecord.view',
            'medicalOrder.view',
            'medicalOrder.create',
            'medicalOrder.update',
            'medicalOrder.delete',
            'medicalOrderDetail.view',
            'medicalOrderDetail.create',
            'medicalOrderDetail.update',
            'medicalOrderDetail.delete',
            'medicationRecord.view',
            'medicationRecordDetail.view',
            'medicationNotification.view',
        ]);

        $role = Role::findByName('receptionist');
        $role->syncPermissions([
            'admission.view',
            'admission.create',
            'patient.view',
            'patient.create',
            'patient.update',
            'user.view',
        ]);

        $role = Role::findByName('nurse');
        $role->syncPermissions([
            'admission.view',
            'admission.update',
            'temperatureRecord.view',
            'temperatureRecord.create',
            'temperatureRecord.update',
            'temperatureDetail.*',
            'bed.update',
            'patient.view',
            'nurseRecord.view',
            'nurseRecord.create',
            'nurseRecord.update',
            'nurseRecord.delete',
            'nurseRecordDetail.*',
            'nurseRecordDetail.view',
            'nurseRecordDetail.update',
            'nurseRecordDetail.delete',
            'nurseRecordDetail.delete',
            'medicationRecord.view',
            'medicationRecord.create',
            'medicationRecord.update',
            'medicationRecord.delete',
            'medicationRecordDetail.view',
            'medicationRecordDetail.create',
            'medicationRecordDetail.update',
            'medicationRecordDetail.delete',
            'medicalOrder.view',
            'medicalOrderDetail.view',
            'medicationNotification.view',
            'medicationNotification.create',
            'medicationNotification.update',

        ]);

        $user = User::where('name', 'Test User')->first();
        $user->syncRoles('admin');

        $user = User::where('name', 'Admin')->first();
        $user->syncRoles('admin');

        // a los usuarios que estan creados en la base de datos, excepto test user asigna roles aleatorios
        $users = User::where('name', '!=', 'Test User')->get();
        $roles = Role::all()->pluck('name')->toArray();

        foreach ($users as $user) {
            $randomRole = $roles[array_rand($roles)];
            $user->assignRole($randomRole);
        }
    }
}
