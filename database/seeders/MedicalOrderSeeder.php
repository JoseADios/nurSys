<?php

namespace Database\Seeders;

use App\Models\Admission;
use App\Models\MedicalOrder;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MedicalOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $doctors = User::get();
        $admissions = Admission::all();

        // Crear múltiples órdenes médicas
        $medicalOrders = [
            [
                'admission_id' => $admissions->first()->id,
                'doctor_id' => $doctors->first()->id,
                'doctor_sign' => 'Dr. Smith Signature',
                'active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'admission_id' => $admissions->skip(1)->first()->id,
                'doctor_id' => $doctors->skip(1)->first()->id,
                'doctor_sign' => 'Dr. Johnson Digital Sign',
                'active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'admission_id' => $admissions->skip(2)->first()->id,
                'doctor_id' => $doctors->skip(2)->first()->id,
                'doctor_sign' => 'Dr. Williams Electronic Sign',
                'active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        DB::table('medical_orders')->insert($medicalOrders);
    }
}
