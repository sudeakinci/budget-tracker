<?php

namespace Database\Factories;

use App\Models\Bank;
use App\Models\User;
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
            "user_id" => User::query()->inRandomOrder()->first()?->id ?? User::factory(),
            "bank_id" => Bank::query()->inRandomOrder()->first()?->id ?? Bank::factory(),
            'is_income' => $this->faker->boolean(),
            "amount" => $this->faker->randomFloat(2, 10, 500),
            "description" => $this->faker->sentence(),
            "date" => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
