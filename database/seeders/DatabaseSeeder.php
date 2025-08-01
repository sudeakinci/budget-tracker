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

        $this->call(PaymentTermSeeder::class);

        // create 10 users
        User::factory(10)->create()->each(function ($user) {
            // create 2 custom payment terms for each user
            PaymentTerm::factory(2)->create([
                'user_id' => $user->id,
            ]);
        });

        // create 50 transactions
        Transaction::factory(50)->create();
    }
}