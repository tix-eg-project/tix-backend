<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Product1VariantsSeeder extends Seeder
{
    public function run(): void
    {
        $productId = 1;

        // سلامة: لو المنتج مش موجود، بلاش نكمل
        $exists = DB::table('products')->where('id', $productId)->exists();
        if (!$exists) return;

        // امسح أي Variants/Values قديمة للمنتج 1 علشان منكررهاش
        if (Schema::hasTable('product_variants')) {
            $variantIds = DB::table('product_variants')
                ->where('product_id', $productId)
                ->pluck('id')
                ->toArray();

            if ($variantIds) {
                if (Schema::hasTable('product_variant_values')) {
                    DB::table('product_variant_values')->whereIn('variant_id', $variantIds)->delete();
                }
                DB::table('product_variants')->whereIn('id', $variantIds)->delete();
            }
        }

        // أنشئ Variants: Color + Size
        $namePayload = fn($ar, $en) => json_encode(['ar' => $ar, 'en' => $en], JSON_UNESCAPED_UNICODE);

        $colorId = DB::table('product_variants')->insertGetId([
            'product_id' => $productId,
            // لو عمود name عندك JSON فالـ json_encode هيمشي تمام؛ لو نص عادي هيّتخزن كنص عادي برضه
            'name'       => $namePayload('اللون', 'Color'),
        ]);

        $sizeId = DB::table('product_variants')->insertGetId([
            'product_id' => $productId,
            'name'       => $namePayload('المقاس', 'Size'),
        ]);

        // قيم Color
        foreach ([
            ['ar' => 'أحمر', 'en' => 'Red'],
            ['ar' => 'أزرق', 'en' => 'Blue'],
            ['ar' => 'فضي',  'en' => 'Silver'],
        ] as $c) {
            DB::table('product_variant_values')->insert([
                'variant_id' => $colorId,
                // لو عندك أعمدة option_name/option_value استخدمها؛ غير كده نحفظ في عمود value (JSON)
                'option_name'  => Schema::hasColumn('product_variant_values', 'option_name')  ? 'Color' : null,
                'option_value' => Schema::hasColumn('product_variant_values', 'option_value') ? $c['en'] : null,
                'value'        => Schema::hasColumn('product_variant_values', 'value') ? $namePayload($c['ar'], $c['en']) : null,
            ]);
        }

        // قيم Size
        foreach ([
            ['ar' => '12.5',  'en' => '12.5'],
            ['ar' => '15.75', 'en' => '15.75'],
            ['ar' => '17',    'en' => '17'],
        ] as $s) {
            DB::table('product_variant_values')->insert([
                'variant_id' => $sizeId,
                'option_name'  => Schema::hasColumn('product_variant_values', 'option_name')  ? 'Size' : null,
                'option_value' => Schema::hasColumn('product_variant_values', 'option_value') ? $s['en'] : null,
                'value'        => Schema::hasColumn('product_variant_values', 'value') ? $namePayload($s['ar'], $s['en']) : null,
            ]);
        }

        // (اختياري) نزود صورتين للمنتج 1 علشان ما تبقاش images فاضية
        if (Schema::hasTable('product_images')) {
            // امسح صور قديمة لو أي
            DB::table('product_images')->where('product_id', $productId)->delete();

            DB::table('product_images')->insert([
                [
                    'product_id' => $productId,
                    'image'      => 'public/image/products/p1_main.jpg',
                    'path'       => 'products/p1_main.jpg',
                    'is_primary' => Schema::hasColumn('product_images', 'is_primary') ? 1 : null,
                    'sort_order' => Schema::hasColumn('product_images', 'sort_order') ? 1 : null,
                ],
                [
                    'product_id' => $productId,
                    'image'      => 'public/image/products/p1_side.jpg',
                    'path'       => 'products/p1_side.jpg',
                    'is_primary' => Schema::hasColumn('product_images', 'is_primary') ? 0 : null,
                    'sort_order' => Schema::hasColumn('product_images', 'sort_order') ? 2 : null,
                ],
            ]);
        }
    }
}
