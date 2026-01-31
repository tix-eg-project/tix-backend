<?php

namespace Database\Seeders;

use App\Models\Vendor;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class VendorSeeder extends Seeder
{
    public static int $count = 2;
    public function run(): void
    {
        for ($i = 0; $i < self::$count; $i++) {
            Vendor::create([
                'company_name' => 'Vendor ' . $i,
                'email' => 'vendor' . $i . '@vendor.com',
                'password' => Hash::make('password123'),
                'description' => 'description',
                'phone' => '1234567890',
                'name' => 'Ahmed',
                'address' => 'address',
                'postal_code' => '12345',
                'vodafone_cash' => 'vodafone_cash',
                'instapay' => 'instapay',
                'status' => 0,
                'type_business' => 1,
                'category_id' => rand(1, CategorySeed::$count),
                'country_id' => rand(1, CountrySeed::$count),
                'city_id' => rand(1, CitySeed::$count),
            ]);
        }
    }
}
