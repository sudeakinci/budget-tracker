<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\PaymentTerm;
use App\Models\Transaction;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // clear the tables
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        Transaction::truncate();
        PaymentTerm::truncate();
        User::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // create 10 users
        User::factory(10)->create()->each(function ($user) {
            // create 2 payment methods for each user
            PaymentTerm::factory(2)->create([
                'user_id' => $user->id,
            ])->each(function ($paymentTerm) use ($user) {
                // create 5 transactions for each payment method
                Transaction::factory(5)->create([
                    'owner' => $user->id,
                    // user_id sometimes null, sometimes a different user
                    'user_id' => (rand(0, 1) === 1)
                        ? User::where('id', '!=', $user->id)->inRandomOrder()->first()?->id
                        : null,
                    'payment_term' => $paymentTerm->name,
                ]);
            });
        });
    }
}