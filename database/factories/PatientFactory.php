<?php

namespace Database\Factories;

use App\Models\Ars;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Patient>
 */
class PatientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => $this->faker->firstName(),
            'first_surname' => $this->faker->lastName(),
            'second_surname' => $this->faker->lastName(),
            'phone' => $this->faker->phoneNumber(),
            'identification_card' => $this->faker->phoneNumber(),
            'nationality' => $this->faker->country(),
            'email' => $this->faker->email(),
            'birthdate' => $this->faker->date(),
            'position' => $this->faker->jobTitle(),
            'marital_status' => $this->faker->randomElement(['Single', 'Married', 'Divorced']),
            'address' => $this->faker->address(),
            'ars' => Ars::inRandomOrder()->first()->name,
            'active' => true,
        ];
    }
}
