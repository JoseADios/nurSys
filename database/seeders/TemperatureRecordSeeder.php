<?php

namespace Database\Seeders;

use App\Models\TemperatureRecord;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TemperatureRecordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TemperatureRecord::create([
            'admission_id' => 1,
            'nurse_id' => 1,
            'impression_diagnosis' => 'Diagnosis 1',
        ]);
        TemperatureRecord::create([
            'admission_id' => 2,
            'nurse_id' => 2,
            'impression_diagnosis' => 'Leucemia',
        ]);
        TemperatureRecord::create([
            'admission_id' => 3,
            'nurse_id' => 1,
            'impression_diagnosis' => 'Diagnosis 3',
        ]);
        TemperatureRecord::create([
            'admission_id' => 4,
            'nurse_id' => 2,
            'impression_diagnosis' => 'Diagnosis 4',
        ]);
    }
}
