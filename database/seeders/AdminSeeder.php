<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing admins to avoid duplicates
        DB::table('admins')->truncate();

        // Insert admin accounts
        DB::table('admins')->insert([
            [
                'name' => 'Super Admin',
                'email' => 'admin@tix.com',
                'phone' => '+966501234567',
                'password' => Hash::make('Admin@123456'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Support Admin',
                'email' => 'support@tix.com',
                'phone' => '+966502345678',
                'password' => Hash::make('Support@123456'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Manager Admin',
                'email' => 'manager@tix.com',
                'phone' => '+966503456789',
                'password' => Hash::make('Manager@123456'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $this->command->info('✅ 3 Admin accounts created successfully!');
    }
}
