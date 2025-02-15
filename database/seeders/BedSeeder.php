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
        foreach (range(1, 3) as $floor) {
            foreach (range(1, 2) as $room) {
                foreach (range(1, 6) as $number) {
                    DB::table('beds')->insert([
                        'number' => $number,
                        'room' => $floor . '0' . $room,
                        'floor' => $floor,
                        'active' => true,
                    ]);
                }
            }
        }
    }
}
