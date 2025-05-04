<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MedicationRecord>
 */
class MedicationRecordFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'admission_id' => $this->faker->numberBetween(1, 10), // Cambia el rango según los IDs de Admission

            'diet' => $this->faker->word(),
            'referrals' => $this->faker->sentence(),
            'pending_studies' => $this->faker->sentence(),
            'doctor_sign' => $this->faker->name(),
            'active' => $this->faker->boolean(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
