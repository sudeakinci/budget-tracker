<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class SmsSettingsTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('sms_settings')->insert([
            [
                'id' => 1,
                'bank_name' => 'Ziraat',
                'payment_term_id' => 4,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 2,
                'bank_name' => 'Vakıf Bank',
                'payment_term_id' => 5,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
                       [
                'id' => 6,
                'bank_name' => 'İş Bankası',
                'payment_term_id' => 6,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
