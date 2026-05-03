<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AllTablesSeeder extends Seeder
{
    public function run(): void
    {
        // Countries
        for ($i = 1; $i <= 5; $i++) {
            DB::table('countries')->insert([
                'name' => json_encode(['en' => "Country $i", 'ar' => "الدولة $i"])
            ]);
        }

        // Cities
        $countryIds = DB::table('countries')->pluck('id')->toArray();
        foreach ($countryIds as $countryId) {
            for ($i = 1; $i <= 5; $i++) {
                DB::table('cities')->insert([
                    'country_id' => $countryId,
                    'name'       => json_encode(['en' => "City $i", 'ar' => "المدينة $i"])
                ]);
            }
        }

        // Categories
        for ($i = 1; $i <= 5; $i++) {
            DB::table('categories')->insert([
                'name' => json_encode(['en' => "Category $i", 'ar' => "الفئة $i"]),
                'image' => 'public/image/default.jpg'
            ]);
        }

        // Subcategories
        $categoryIds = DB::table('categories')->pluck('id')->toArray();
        foreach ($categoryIds as $catId) {
            for ($i = 1; $i <= 5; $i++) {
                DB::table('subcategories')->insert([
                    'category_id' => $catId,
                    'name'        => json_encode(['en' => "Subcategory $i", 'ar' => "الفئة الفرعية $i"]),
                    'description' => json_encode(['en' => "Description for subcategory $i", 'ar' => "وصف الفئة الفرعية $i"]),
                    'image'       => 'public/image/default.jpg',
                ]);
            }
        }


        // Brands
        for ($i = 1; $i <= 5; $i++) {
            DB::table('brands')->insert([
                'name' => json_encode(['en' => "Brand $i", 'ar' => "العلامة $i"])
            ]);
        }

        // Offers
        for ($i = 1; $i <= 5; $i++) {
            DB::table('offers')->insert([
                'name'        => json_encode(['en' => "Offer $i", 'ar' => "العرض $i"]),
                'description' => json_encode(['en' => "Offer description $i", 'ar' => "وصف العرض $i"])
            ]);
        }

        // Products
        $brandIds = DB::table('brands')->pluck('id')->toArray();
        foreach ($categoryIds as $categoryId) {
            foreach ($brandIds as $brandId) {
                for ($i = 1; $i <= 5; $i++) {
                    DB::table('products')->insert([
                        'name'         => json_encode(['en' => "Product $i", 'ar' => "المنتج $i"]),
                        'category_id'  => $categoryId,
                        'brand_id'     => $brandId,
                        'price'        => rand(100, 500),
                        'description'  => json_encode(['en' => "Description $i", 'ar' => "الوصف $i"]),
                        'image'        => 'public/image/default.jpg'
                    ]);
                }
            }
        }

        // Offer products pivot
        $offerIds = DB::table('offers')->pluck('id')->toArray();
        $productIds = DB::table('products')->pluck('id')->toArray();
        foreach ($offerIds as $offerId) {
            foreach (array_slice($productIds, 0, 5) as $productId) {
                DB::table('offer_product')->insert([
                    'offer_id'   => $offerId,
                    'product_id' => $productId
                ]);
            }
        }

        // Attribute definitions
        for ($i = 1; $i <= 5; $i++) {
            DB::table('attribute_definitions')->insert([
                'name' => json_encode(['en' => "Attribute $i", 'ar' => "السمة $i"]),
                'type' => 'text'
            ]);
        }

        // Product attributes (fixed for unique composite key)
        $attributeIds = DB::table('attribute_definitions')->pluck('id')->toArray();
        foreach ($productIds as $productId) {
            $idxCounter = 1;
            foreach (array_slice($attributeIds, 0, min(5, count($attributeIds))) as $attributeId) {
                DB::table('product_attributes')->insert([
                    'product_id'      => $productId,
                    'attribute_id'    => $attributeId,
                    'idx'             => $idxCounter++,
                    'value_bool'      => (bool)random_int(0, 1),
                    'value_date'      => now(),
                    'value_list_item' => "Item {$attributeId}",
                    'value_number'    => random_int(10, 100),
                    'value_text'      => "Value {$attributeId}"
                ]);
            }
        }

        // Product images
        foreach ($productIds as $productId) {
            for ($i = 1; $i <= 5; $i++) {
                DB::table('product_images')->insert([
                    'product_id' => $productId,
                    'image'      => 'public/image/default.jpg'
                ]);
            }
        }

        // Product variants
        for ($i = 1; $i <= 5; $i++) {
            DB::table('product_variants')->insert([
                'name' => json_encode(['en' => "Variant $i", 'ar' => "المتغير $i"])
            ]);
        }

        // Product variant values
        $variantIds = DB::table('product_variants')->pluck('id')->toArray();
        foreach ($variantIds as $variantId) {
            for ($i = 1; $i <= 5; $i++) {
                DB::table('product_variant_values')->insert([
                    'variant_id' => $variantId,
                    'value'      => json_encode(['en' => "Value $i", 'ar' => "القيمة $i"])
                ]);
            }
        }
    }
}
