<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Finance>
 */
class FinanceFactory extends Factory
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
            'name_item' => $this->faker->word(),
            'category_finances_id' => 1,
            'price' => $this->faker->numberBetween(1000, 100000),
            'purchase_date' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'purchase_by' => $this->faker->name(),
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => null,
        ];
    }
}
