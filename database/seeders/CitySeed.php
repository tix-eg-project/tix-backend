<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CitySeed extends Seeder
{
    public static int $count = 11;
    public function run(): void
    {
        for ($i = 1; $i <= self::$count; $i++) {
            City::create([
                'name' => [
                    'ar' => 'القاهرة',
                    'en' => 'Cairo',
                ],
                'country_id' => $i,
            ]);
        }
    }
}
