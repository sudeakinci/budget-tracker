<?php

namespace Database\Factories;

use App\Models\Account;
use App\Models\User;
use App\Models\TransactionType;
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
        $sender = User::inRandomOrder()->first() ?? User::factory()->create();
        do {
            $receiver = User::inRandomOrder()->first() ?? User::factory()->create();
        } while ($receiver->id === $sender->id);

        // accounts
        $senderAccount = Account::where('user_id', $sender->id)->inRandomOrder()->first() ?? Account::factory()->create(['user_id' => $sender->id]);
        $receiverAccount = Account::where('user_id', $receiver->id)->inRandomOrder()->first() ?? Account::factory()->create(['user_id' => $receiver->id]);

        $transactionType = TransactionType::inRandomOrder()->first() ?? TransactionType::factory()->create();

        return [
            'sender_account_id' => $senderAccount->id,
            'sender_id' => $sender->id,
            'receiver_account_id' => $receiverAccount->id,
            'receiver_id' => $receiver->id,
            'amount' => $this->faker->randomFloat(2, 10, 500),
            'is_income' => $this->faker->boolean(),
            'description' => $this->faker->sentence(),
            'transaction_type_id' => $transactionType->id,
            'date' => now(),
        ];
    }
}
