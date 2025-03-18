<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MedicationRecord;
use Illuminate\Support\Facades\DB;

class MedicationRecordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        MedicationRecord::create([
            'admission_id' => 1,
            'doctor_id' =>1, // Asegúrate de que exista un Admission con id=1
            'diagnosis' => 'Diabetes tipo 2',
            'diet' => 'Baja en carbohidratos',
            'doctor_sign' => 'Dr. Pérez',
            'active' => true,
        ]);

        // Puedes agregar más registros de la misma forma
        MedicationRecord::create([
            'admission_id' => 2, // Asegúrate de que exista un Admission con id=2
            'doctor_id' => 2, // Asegúrate de que exista un Admission con id=2
            'diagnosis' => 'Hipertensión',
            'diet' => 'Baja en sodio',
            'doctor_sign' => 'Dr. López',
            'active' => true,
        ]);
    }
}
