<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // admission
            'create-admission',
            'update-admission',
            'view-admission',
            'delete-admission',

            // medical orders
            'create-medicalOrder',
            'update-medicalOrder',
            'view-medicalOrder',
            'delete-medicalOrder',

            // medical order detail
            'create-medicalOrderDetail',
            'update-medicalOrderDetail',
            'view-medicalOrderDetail',
            'delete-medicalOrderDetail',

            // medication notification
            'create-medicationNotification',
            'update-medicationNotification',
            'view-medicationNotification',
            'delete-medicationNotification',

            // medication Record
            'create-medicationRecord',
            'update-medicationRecord',
            'view-medicationRecord',
            'delete-medicationRecord',

            // medication Record Detail
            'create-medicationRecordDetail',
            'update-medicationRecordDetail',
            'view-medicationRecordDetail',
            'delete-medicationRecordDetail',

            // nurse record
            'create-nurseRecord',
            'update-nurseRecord',
            'view-nurseRecord',
            'delete-nurseRecord',

            // recordDetail
            'create-nurseRecordDetail',
            'update-nurseRecordDetail',
            'view-nurseRecordDetail',
            'delete-nurseRecordDetail',

            // patient
            'create-patient',
            'update-patient',
            'view-patient',
            'delete-patient',

            // temperatureDetail
            'create-temperatureDetail',
            'update-temperatureDetail',
            'view-temperatureDetail',
            'delete-temperatureDetail',

            // temperatureRecord
            'create-temperatureRecord',
            'update-temperatureRecord',
            'view-temperatureRecord',
            'delete-temperatureRecord',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

    }
}
