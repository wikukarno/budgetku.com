<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Salary>
 */
class SalaryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'users_id' => 1,
            'salary' => $this->faker->numberBetween(1000, 100000),
            'date' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'tipe' => 1,
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => null,
            'description' => $this->faker->sentence(),
        ];
    }
}
