<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeed extends Seeder
{
    public static int $count = 11;
    public function run(): void
    {
        for ($i = 0; $i < self::$count; $i++) {
            Category::create([
                'name' => [
                    'ar' => 'تصنيف ' . $i,
                    'en' => 'Category ' . $i
                ]
            ]);
        }
    }
}
