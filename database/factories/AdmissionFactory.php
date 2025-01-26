<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Admission>
 */
class AdmissionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'bed_id' => $this->faker->randomElement(range(1, 10)),
            'patient_id' => $this->faker->randomElement(range(1, 10)),
            'receptionist_id' => $this->faker->randomElement(range(1, 10)),
            'doctor_id' => $this->faker->randomElement(range(1, 10)),
            'admission_dx' => $this->faker->sentence(),
            'final_dx' => $this->faker->sentence(),
            'created_at' => now(),
            'comment' => $this->faker->sentence(),
            'active' => true,
        ];
    }
}
