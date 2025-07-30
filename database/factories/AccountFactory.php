<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Bank;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Account>
 */
class AccountFactory extends Factory
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
            "balance" => $this->faker->randomFloat(2, 0, 10000),
            "created_at" => now(),
            "updated_at" => now(),
        ];
    }
}
