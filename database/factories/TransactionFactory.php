<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    public function definition(): array
    {
        $owner = User::inRandomOrder()->first() ?? User::factory()->create();

        // user_id sometimes null, sometimes a different user
        $user = (rand(0, 1) === 1)
            ? (User::where('id', '!=', $owner->id)->inRandomOrder()->first() ?? User::factory()->create())
            : null;

        return [
            'owner' => $owner->id,
            'user_id' => $user?->id,
            'payment_term' => $this->faker->randomElement(['nakit', 'havale', 'eft', 'kredi kartı']),
            'description' => $this->faker->sentence(),
            'amount' => $this->faker->randomFloat(2, 10, 10000),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}