<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class TransactionTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('transactions')->insert([
            [
                'id' => 1,
                'owner' => 2,
                'user_id' => 3,
                'payment_term_id' => 1,
                'payment_term_name' => 'Kredi Kartı',
                'description' => 'Elektrik faturası',
                'amount' => 250.75,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 2,
                'owner' => 3,
                'user_id' => 2,
                'payment_term_id' => 2,
                'payment_term_name' => 'Havale',
                'description' => 'Kira ödemesi',
                'amount' => 1000.00,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 3,
                'owner' => 2,
                'user_id' => 3,
                'payment_term_id' => 1,
                'payment_term_name' => 'Kredi Kartı',
                'description' => 'Market alışverişi',
                'amount' => 125.00,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 4,
                'owner' => 2,
                'user_id' => 3,
                'payment_term_id' => 1,
                'payment_term_name' => 'Kredi Kartı',
                'description' => 'İnternet faturası',
                'amount' => 89.99,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 5,
                'owner' => 3,
                'user_id' => 2,
                'payment_term_id' => 2,
                'payment_term_name' => 'Havale',
                'description' => 'Aidat ödemesi',
                'amount' => 150.00,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 6,
                'owner' => 2,
                'user_id' => 3,
                'payment_term_id' => 3,
                'payment_term_name' => 'Nakit',
                'description' => 'Kırtasiye harcaması',
                'amount' => 45.50,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 7,
                'owner' => 2,
                'user_id' => 3,
                'payment_term_id' => 1,
                'payment_term_name' => 'Kredi Kartı',
                'description' => 'Telefon faturası',
                'amount' => 110.25,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 8,
                'owner' => 3,
                'user_id' => 2,
                'payment_term_id' => 2,
                'payment_term_name' => 'Havale',
                'description' => 'Su faturası',
                'amount' => 75.00,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 9,
                'owner' => 2,
                'user_id' => 3,
                'payment_term_id' => 1,
                'payment_term_name' => 'Kredi Kartı',
                'description' => 'Giyim harcaması',
                'amount' => 300.00,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 10,
                'owner' => 3,
                'user_id' => 2,
                'payment_term_id' => 2,
                'payment_term_name' => 'Havale',
                'description' => 'Alışveriş iadesi',
                'amount' => -50.00,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
