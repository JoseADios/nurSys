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
            'dose' => '100cc',
            'route' => 'Oral',
            'fc' => 2,
            'start_time'=> '',
            'interval_in_hours' => 4,
            'active' => true,
        ]);

        // Puedes agregar más registros de la misma forma
        MedicationRecordDetail::create([
            'medication_record_id' => 2,
            'drug' => 'Paracetamol',
            'dose' => '200cc',
            'route' => 'Intra Venosa',
            'fc' => 1,
            'start_time'=> '',
            'interval_in_hours' => 2,
            'active' => true,
        ]);
    }
}
