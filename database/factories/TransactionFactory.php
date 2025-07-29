<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "type" => $this->faker->randomElement(['income', 'outcome']),
            "amount" => $this->faker->randomFloat(2, 10, 500),
            "description" => $this->faker->sentence(),
            "date" => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
