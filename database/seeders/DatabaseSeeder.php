<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\PaymentTermsTableSeeder;
use Database\Seeders\TransactionTableSeeder;
use Database\Seeders\UsersTableSeeder;
use Database\Seeders\SmsSettingsTableSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UsersTableSeeder::class,
            PaymentTermsTableSeeder::class,
            TransactionTableSeeder::class,
            SmsSettingsTableSeeder::class,
        ]);
    }
}