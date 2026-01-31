<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;

class CountrySeed extends Seeder
{
  public static int $count = 11;    

    public function run(): void
    {
        for ($i = 0; $i < self::$count; $i++) {
            Country::create([
                'name' => [
                    'ar' => 'مصر',
                    'en' => 'Egypt',
                ],
            ]);
        }
    }
}
