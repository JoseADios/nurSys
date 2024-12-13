<?php

namespace Database\Seeders;

use App\Models\Room;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Room::create([
            'name' => '101',
            'floor' => 1,
            'description' => 'Esta es una descripcion de la sala 101',
        ]);

        Room::create([
            'name' => '102',
            'floor' => 1,
            'description' => 'Esta es una descripcion de la sala 102',
        ]);

        Room::create([
            'name' => '103',
            'floor' => 1,
            'description' => 'Esta es una descripcion de la sala 103',
        ]);

        Room::create([
            'name' => '204',
            'floor' => 2,
            'description' => 'Esta es una descripcion de la sala 204',
        ]);

    }
}
