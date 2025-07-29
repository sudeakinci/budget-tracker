<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Bank;
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
        // clen the tables before seeding for a fresh start
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        User::truncate();
        Bank::truncate();
        Transaction::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        User::factory(10)->create()->each(function ($user) {
            // for each user, create 2 bank accounts
            Bank::factory(2)->create([
                'user_id' => $user->id,
            ])->each(function ($bank) {
                // for each bank account, create 5 transactions
                Transaction::factory(5)->create([
                    'user_id' => $bank->user_id,
                    'bank_id' => $bank->id,
                ]);
            });
        });
    }
}
