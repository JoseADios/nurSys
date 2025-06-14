<?php

namespace Database\Seeders;

use App\Models\MedicationNotification;
use Auth;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MedicationNotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * @return void
     */
    public function run(): void
    {
        MedicationNotification::create([
            'medication_record_detail_id' => 1,
            'nurse_id' => Auth::id(),
            'applied' => 0,
            'scheduled_time' => now()->addHours(2),
            'administered_time' => now()->addHours(2),
            'active' => true,
            'nurse_sign'=>null,
        ]);


    }
}
