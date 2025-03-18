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
            'bed.view',
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
            'admission.*',
            'bed.view',
            'patient.view',
            'patient.create',
            'patient.update',
        ]);

        $role = Role::findByName('nurse');
        $role->syncPermissions([
            'admission.view',
            'temperatureRecord.view',
            'temperatureRecord.create',
            'temperatureRecord.update',
            'temperatureDetail.*',
            'bed.view',
            'bed.update',
            'bed.delete',
            'bed.create',
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
