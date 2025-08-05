<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;

class UsersTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'id' => 1,
                'name' => 'Harun Korkmaz',
                'email' => 'harun@gmail.com',
                'email_verified_at' => null,
                'password' => Hash::make('password'),
                'remember_token' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'balance' => 10000.00,
                'deleted_at' => null,
            ],
            [
                'id' => 2,
                'name' => 'Sude Akıncı',
                'email' => 'sude@gmail.com',
                'email_verified_at' => null,
                'password' => Hash::make('password'),
                'remember_token' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'balance' => 500.00,
                'deleted_at' => null,
            ],
            [
                'id' => 3,
                'name' => 'Ahmet Demir',
                'email' => 'ahmet@gmail.com',
                'email_verified_at' => null,
                'password' => Hash::make('password'),
                'remember_token' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'balance' => 750.00,
                'deleted_at' => null,
            ],
            [
                'id' => 4,
                'name' => 'Zeynep Kaya',
                'email' => 'zeynep@gmail.com',
                'email_verified_at' => null,
                'password' => Hash::make('password'),
                'remember_token' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'balance' => 1200.00,
                'deleted_at' => null,
            ],
            [
                'id' => 5,
                'name' => 'Mehmet Çelik',
                'email' => 'mehmet@gmail.com',
                'email_verified_at' => null,
                'password' => Hash::make('password'),
                'remember_token' => null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'balance' => 300.00,
                'deleted_at' => null,
            ],
        ]);
    }
}
