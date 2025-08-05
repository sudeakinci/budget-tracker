<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class PaymentTermsTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('payment_terms')->insert([
            [
                'id' => 1,
                'name' => 'Kredi Kartı',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => null,
            ],
            [
                'id' => 2,
                'name' => 'Havale',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => null,
            ],
            [
                'id' => 3,
                'name' => 'Nakit',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => null,
            ],
            [
                'id' => 4,
                'name' => 'Ziraat Bankası',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => null,
            ],
            [
                'id' => 5,
                'name' => 'Vakıf Bank',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => null, 
            ],
            [
                'id' => 6,
                'name' => 'İş Bankası',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'created_by' => null,
            ],
        ]);
    }
}
