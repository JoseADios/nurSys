<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BedSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (range(1, 10) as $number) {
            DB::table('beds')->insert([
                'number' => $number,
                'room' => '1',
                'status' => 'free',
                'active' => true,
            ]);
        }
    }
}
