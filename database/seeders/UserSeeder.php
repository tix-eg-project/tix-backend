<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public static int $count = 11;
    public function run(): void
    {
        for ($i = 0; $i < self::$count; $i++) {
            User::create([
                'name' => 'User ' . $i,
                'email' => 'user' . $i . '@user.com',
                'password' => Hash::make('password123'),
            ]);
        }
    }
}
