<?php

namespace Database\Seeders;

use App\Models\PaymentTerm;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaymentTermSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PaymentTerm::create(['name' => 'Cash']);
        PaymentTerm::create(['name' => 'Credit Card']);
        PaymentTerm::create(['name' => 'Bank Transfer']);
    }
}
