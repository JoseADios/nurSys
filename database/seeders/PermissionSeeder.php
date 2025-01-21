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
            'admission.*',
            'admission.create',
            'admission.update',
            'admission.view',
            'admission.delete',

            // medical orders
            'medicalOrder.*',
            'medicalOrder.create',
            'medicalOrder.update',
            'medicalOrder.view',
            'medicalOrder.delete',

            // medical order detail
            'medicalOrderDetail.*',
            'medicalOrderDetail.create',
            'medicalOrderDetail.update',
            'medicalOrderDetail.view',
            'medicalOrderDetail.delete',

            // medication notification
            'medicationNotification.*',
            'medicationNotification.create',
            'medicationNotification.update',
            'medicationNotification.view',
            'medicationNotification.delete',

            // medication Record
            'medicationRecord.*',
            'medicationRecord.create',
            'medicationRecord.update',
            'medicationRecord.view',
            'medicationRecord.delete',

            // medication Record Detail
            'medicationRecordDetail.*',
            'medicationRecordDetail.create',
            'medicationRecordDetail.update',
            'medicationRecordDetail.view',
            'medicationRecordDetail.delete',

            // nurse record
            'nurseRecord.*',
            'nurseRecord.create',
            'nurseRecord.update',
            'nurseRecord.view',
            'nurseRecord.delete',

            // recordDetail
            'nurseRecordDetail.*',
            'nurseRecordDetail.create',
            'nurseRecordDetail.update',
            'nurseRecordDetail.view',
            'nurseRecordDetail.delete',

            // patient
            'patient.*',
            'patient.create',
            'patient.update',
            'patient.view',
            'patient.delete',

            // temperatureDetail
            'temperatureDetail.*',
            'temperatureDetail.create',
            'temperatureDetail.update',
            'temperatureDetail.view',
            'temperatureDetail.delete',

            // temperatureRecord
            'temperatureRecord.*',
            'temperatureRecord.create',
            'temperatureRecord.update',
            'temperatureRecord.view',
            'temperatureRecord.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

    }
}
