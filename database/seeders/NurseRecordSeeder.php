<?php

namespace Database\Seeders;

use App\Models\NurseRecord;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NurseRecordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        NurseRecord::create([
            'admission_id' => 1,
            'nurse_id' => 1,
            'created_at' => now(),
            'active' => true,
        ]);

        NurseRecord::create([
            'admission_id' => 1,
            'nurse_id' => 2,
            'created_at' => now(),
            'active' => true,
        ]);

        NurseRecord::create([
            'admission_id' => 2,
            'nurse_id' => 2,
            'created_at' => now(),
            'active' => true,
        ]);


    }
}
