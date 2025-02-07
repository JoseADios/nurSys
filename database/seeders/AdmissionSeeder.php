<?php

namespace Database\Seeders;

use App\Models\Admission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

use function Laravel\Prompts\table;

class AdmissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (range(1, 10) as $i) {
            DB::table('admissions')->insert([
                'bed_id' => $i,
                'patient_id' => $i,
                'receptionist_id' => rand(1, 10),
                'doctor_id' => rand(1, 10),
                'admission_dx' => Str::random(10),
                'final_dx' => $i % 2 ? null : Str::random(),
                'created_at' => now(),
                'comment' => Str::random(),
            ]);
        }
    }
}
