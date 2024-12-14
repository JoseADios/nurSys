<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NurseRecordDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $nurseRecords = DB::table('nurse_records')->pluck('id');

        $details = [
            [
                'nurse_record_id' => $nurseRecords->random(),
                'medication' => 'Paracetamol 500mg',
                'comment' => 'Administrado para reducir la fiebre',
                'active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nurse_record_id' => $nurseRecords->random(),
                'medication' => 'Ibuprofeno 400mg',
                'comment' => 'Para aliviar el dolor',
                'active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nurse_record_id' => $nurseRecords->random(),
                'medication' => 'Amoxicilina 250mg',
                'comment' => 'Antibiótico prescrito',
                'active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nurse_record_id' => $nurseRecords->random(),
                'medication' => 'Metamizol 575mg',
                'comment' => 'Analgésico para dolor intenso',
                'active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nurse_record_id' => $nurseRecords->random(),
                'medication' => 'Omeprazol 20mg',
                'comment' => 'Para problemas de acidez',
                'active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nurse_record_id' => $nurseRecords->random(),
                'medication' => 'Diclofenaco 50mg',
                'comment' => 'Antiinflamatorio',
                'active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nurse_record_id' => $nurseRecords->random(),
                'medication' => 'Lorazepam 1mg',
                'comment' => 'Ansiolítico para la ansiedad',
                'active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nurse_record_id' => $nurseRecords->random(),
                'medication' => 'Morfina 10mg',
                'comment' => 'Analgésico para dolor severo',
                'active' => false,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nurse_record_id' => $nurseRecords->random(),
                'medication' => 'Tramadol 50mg',
                'comment' => 'Analgésico de acción central',
                'active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'nurse_record_id' => $nurseRecords->random(),
                'medication' => 'Insulina Glargina',
                'comment' => 'Administración de insulina',
                'active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        ];

        DB::table('nurse_record_details')->insert($details);
    }
}
