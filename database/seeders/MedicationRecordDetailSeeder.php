<?php

namespace Database\Seeders;

use App\Models\MedicationRecordDetail;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MedicationRecordDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * @return void
     */
    public function run(): void
    {
        MedicationRecordDetail::create([
            'medication_record_id' => 1,
            'drug' => 'Acetominofen',
            'dose' => '100',
            'dose_metric' => 'cc',
            'route' => 'Oral',
            'fc' => 2,
            'start_time' => now(),
            'interval_in_hours' => 8,
             'nebulization_time' => 10,
            'active' => true,
            'medical_order_detail_id'=>1
        ]);


    }
}
