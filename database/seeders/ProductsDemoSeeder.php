<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

// --- عدّل الأسماء هنا لو عندك أسماء موديلات مختلفة ---
use App\Models\Brand;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\Product;
use App\Models\ProductImage;

// لو عندك أسماء مختلفة لتعريفات/قيم الخصائص والڤاريانتس، غيّرها هنا:
use App\Models\AttributeDefinition;   // أحيانًا اسمها ProductAttributeDefinition
use App\Models\ProductAttribute;      // أحيانًا اسمها ProductAttributeValue
use App\Models\Variant;
use App\Models\VariantValue;

class ProductsDemoSeeder extends Seeder
{
    public function run(): void
    {
        // لو عندك قيود خارجية، ممكن توقفها مؤقتًا (اختياري)
        // Schema::disableForeignKeyConstraints();

        DB::transaction(function () {

            // ========== 0) دوال مساعدة ==========
            $t = fn($ar, $en) => ['ar' => $ar, 'en' => $en];

            // مجموعة خيارات ألوان مترجمة (لاستخدامها في list_options)
            $COLOR_OPTIONS = [
                ['key' => 'red',    'value' => ['ar' => 'أحمر', 'en' => 'Red']],
                ['key' => 'blue',   'value' => ['ar' => 'أزرق', 'en' => 'Blue']],
                ['key' => 'silver', 'value' => ['ar' => 'فضي',  'en' => 'Silver']],
                ['key' => 'black',  'value' => ['ar' => 'أسود', 'en' => 'Black']],
            ];

            $SIZE_OPTIONS = [
                ['key' => '12.5',  'value' => ['ar' => '12.5',  'en' => '12.5']],
                ['key' => '15.75', 'value' => ['ar' => '15.75', 'en' => '15.75']],
                ['key' => '17',    'value' => ['ar' => '17',    'en' => '17']],
            ];

            // ========== 1) براندات / كاتيجوري / سابكاتيجوري ==========
            $brandTech   = Brand::create(['name' => $t('تيك كو', 'Tech Co')]);
            $brandPhone  = Brand::create(['name' => $t('موبايلو', 'Mobilo')]);
            $brandBasic  = Brand::create(['name' => $t('عام', 'Generic')]);

            $catElectronics = Category::create(['name' => $t('إلكترونيات', 'Electronics')]);
            $catAccessories = Category::create(['name' => $t('إكسسوارات', 'Accessories')]);

            $subLaptops = Subcategory::create([
                'name'        => $t('لابتوبات', 'Laptops'),
                'category_id' => $catElectronics->id,
            ]);

            $subPhones = Subcategory::create([
                'name'        => $t('هواتف', 'Phones'),
                'category_id' => $catElectronics->id,
            ]);

            $subCovers = Subcategory::create([
                'name'        => $t('أغلفة جوالات', 'Phone Covers'),
                'category_id' => $catAccessories->id,
            ]);

            // ========== 2) تعريفات الخصائص (Attribute Definitions) ==========
            // نفترض أن جدول التعريفات به عمود subcategory_id + name (json) + group_name (json) + type + list_options (json nullable)
            // الأنواع المدعومة في الريسورس: list, text, number, bool, date

            // --- للابتوبات ---
            $defColorLaptop = AttributeDefinition::create([
                'subcategory_id' => $subLaptops->id,
                'name'           => $t('اللون', 'Color'),
                'group_name'     => $t('الخيارات', 'Options'),
                'type'           => 'list',
                'list_options'   => $COLOR_OPTIONS,
            ]);

            $defScreenSize = AttributeDefinition::create([
                'subcategory_id' => $subLaptops->id,
                'name'           => $t('حجم الشاشة (بوصة)', 'Screen Size (inch)'),
                'group_name'     => $t('الشاشة', 'Display'),
                'type'           => 'number',
            ]);

            $defBacklit = AttributeDefinition::create([
                'subcategory_id' => $subLaptops->id,
                'name'           => $t('لوحة مفاتيح مضيئة', 'Backlit Keyboard'),
                'group_name'     => $t('الميزات', 'Features'),
                'type'           => 'bool',
            ]);

            $defReleaseDate = AttributeDefinition::create([
                'subcategory_id' => $subLaptops->id,
                'name'           => $t('تاريخ الإصدار', 'Release Date'),
                'group_name'     => $t('عام', 'General'),
                'type'           => 'date',
            ]);

            $defCPU = AttributeDefinition::create([
                'subcategory_id' => $subLaptops->id,
                'name'           => $t('المعالج', 'CPU'),
                'group_name'     => $t('الأداء', 'Performance'),
                'type'           => 'text',
            ]);

            $defSizes = AttributeDefinition::create([
                'subcategory_id' => $subLaptops->id,
                'name'           => $t('المقاسات', 'Sizes'),
                'group_name'     => $t('القياسات المتاحه', 'Available Sizes'),
                'type'           => 'list',
                'list_options'   => $SIZE_OPTIONS,
            ]);

            // --- للهواتف ---
            $defColorPhone = AttributeDefinition::create([
                'subcategory_id' => $subPhones->id,
                'name'           => $t('اللون', 'Color'),
                'group_name'     => $t('الخيارات', 'Options'),
                'type'           => 'list',
                'list_options'   => $COLOR_OPTIONS,
            ]);

            $defDualSim = AttributeDefinition::create([
                'subcategory_id' => $subPhones->id,
                'name'           => $t('شريحتين', 'Dual SIM'),
                'group_name'     => $t('الميزات', 'Features'),
                'type'           => 'bool',
            ]);

            // ========== 3) منتجات متنوعة السيناريوهات ==========
            // ملاحظة: نفترض أن أعمدة الأسماء/الوصف في Product مصنفة JSON ومعمولة casts
            // وأن فيه عمود image (legacy) + جدول product_images (مع accessor ->url)

            // --- المنتج A: لابتوب برو (خصم محسوب من before/after + صور متعددة + مواصفات كاملة + متغيرات) ---
            $pA = Product::create([
                'name'                => $t('لابتوب برو', 'Laptop Pro'),
                'short_description'   => $t('أداء عالي', 'High performance'),
                'long_description'    => $t('جهاز مناسب للأعمال الثقيلة.', 'Great for heavy workloads.'),
                'brand_id'            => $brandTech->id,
                'subcategory_id'      => $subLaptops->id,
                'price_before'        => 1500,
                'price_after'         => 1200,   // => خصم 20%
                'discount_percent'    => null,   // متروكة لأننا بنحسب من before/after
                'image'               => null,   // هنستخدم جدول الصور فقط هنا
                'status'              => 1,
            ]);

            // صور حديثة بترتيب + صورة أساسية
            ProductImage::create([
                'product_id' => $pA->id,
                'path'       => 'products/laptop-pro/main.jpg', // accessor هيحوّلها لURL
                'is_primary' => 1,
                'sort_order' => 1,
            ]);
            ProductImage::create([
                'product_id' => $pA->id,
                'path'       => 'products/laptop-pro/side.jpg',
                'is_primary' => 0,
                'sort_order' => 2,
            ]);

            // مواصفات متعددة
            ProductAttribute::create([
                'product_id'     => $pA->id,
                'definition_id'  => $defColorLaptop->id,
                'type'           => 'list',
                'value_list_item' => 'silver',
                // list_options اتركها null علشان يورّث من التعريف
            ]);
            ProductAttribute::create([
                'product_id'     => $pA->id,
                'definition_id'  => $defScreenSize->id,
                'type'           => 'number',
                'value_number'   => 15.6,
            ]);
            ProductAttribute::create([
                'product_id'     => $pA->id,
                'definition_id'  => $defBacklit->id,
                'type'           => 'bool',
                'value_bool'     => true,
            ]);
            ProductAttribute::create([
                'product_id'     => $pA->id,
                'definition_id'  => $defReleaseDate->id,
                'type'           => 'date',
                'value_date'     => '2025-06-01',
            ]);
            ProductAttribute::create([
                'product_id'     => $pA->id,
                'definition_id'  => $defCPU->id,
                'type'           => 'text',
                'value_text'     => $t('إنتل i7 جيل 13', 'Intel i7 13th Gen'),
            ]);
            // نفس الخاصية بحواليْن مختلفين → الريسورس هيوحدهم ويعرضهم بفاصلة
            foreach (['12.5', '15.75'] as $sz) {
                ProductAttribute::create([
                    'product_id'     => $pA->id,
                    'definition_id'  => $defSizes->id,
                    'type'           => 'list',
                    'value_list_item' => $sz,
                ]);
            }

            // متغيرات (خيارات)
            $variantColor = Variant::create([
                'product_id'  => $pA->id,
                'name'        => 'Color',
            ]);
            foreach (['Red', 'Blue', 'Silver'] as $v) {
                VariantValue::create([
                    'variant_id'   => $variantColor->id,
                    'option_name'  => 'Color',
                    'option_value' => $v,
                ]);
            }

            $variantSize = Variant::create([
                'product_id'  => $pA->id,
                'name'        => 'Size',
            ]);
            foreach (['13"', '15"'] as $v) {
                VariantValue::create([
                    'variant_id'   => $variantSize->id,
                    'option_name'  => 'Size',
                    'option_value' => $v,
                ]);
            }

            // --- المنتج B: هاتف اقتصادي (خصم بالنسبة فقط + صورة legacy فقط + مواصفات بسيطة) ---
            $pB = Product::create([
                'name'                => $t('هاتف اقتصادي', 'Budget Phone'),
                'short_description'   => $t('مناسب للاستخدام اليومي', 'Good for daily use'),
                'long_description'    => $t('بطارية تدوم طويلاً.', 'Long battery life.'),
                'brand_id'            => $brandPhone->id,
                'subcategory_id'      => $subPhones->id,
                'price'               => 300,
                'price_before'        => null,
                'price_after'         => null,
                'discount_percent'    => 10,     // الريسورس هيستخدمه طالما مفيش before/after
                'image'               => 'products/phones/budget/cover.jpg', // legacy cover
                'status'              => 1,
            ]);

            // ما نحطّش صور في جدول ProductImage علشان نختبر fallback للصورة legacy
            ProductAttribute::create([
                'product_id'     => $pB->id,
                'definition_id'  => $defDualSim->id,
                'type'           => 'bool',
                'value_bool'     => true,
            ]);
            ProductAttribute::create([
                'product_id'     => $pB->id,
                'definition_id'  => $defColorPhone->id,
                'type'           => 'list',
                'value_list_item' => 'red',
            ]);

            // --- المنتج C: لابتوب بسيط (بدون خصائص، صور حديثة بدون is_primary) ---
            $pC = Product::create([
                'name'                => $t('لابتوب أساسي', 'Basic Laptop'),
                'short_description'   => $t('خيار اقتصادي', 'Budget option'),
                'long_description'    => $t('مناسب للتصفح والعمل الخفيف.', 'For browsing and light work.'),
                'brand_id'            => $brandBasic->id,
                'subcategory_id'      => $subLaptops->id,
                'price'               => 900,   // لا before/after
                'image'               => null,
                'status'              => 1,
            ]);
            // صور بدون is_primary → نشوف الترتيب بالsort_order
            ProductImage::create([
                'product_id' => $pC->id,
                'path'       => 'products/laptop-basic/1.jpg',
                'is_primary' => 0,
                'sort_order' => 5,
            ]);
            ProductImage::create([
                'product_id' => $pC->id,
                'path'       => 'products/laptop-basic/2.jpg',
                'is_primary' => 0,
                'sort_order' => 1,  // هتيجي قبْل اللي فوق
            ]);

            // --- المنتج D: غلاف هاتف (بدون خصائص ولا صور، نشوف سلوك الريسورس مع الفاضي) ---
            $pD = Product::create([
                'name'                => $t('غلاف هاتف شفاف', 'Transparent Phone Cover'),
                'short_description'   => $t('خامة مرنة', 'Flexible material'),
                'long_description'    => null,
                'brand_id'            => $brandBasic->id,
                'subcategory_id'      => $subCovers->id,
                'price'               => 50,
                'status'              => 1,
            ]);

            // --- المنتج E: لابتوب بخصم صفري (before = after) للتأكد إن الخصم = 0 ---
            $pE = Product::create([
                'name'                => $t('لابتوب ستاندرد', 'Standard Laptop'),
                'short_description'   => $t('متوازن', 'Balanced'),
                'long_description'    => null,
                'brand_id'            => $brandTech->id,
                'subcategory_id'      => $subLaptops->id,
                'price_before'        => 1100,
                'price_after'         => 1100, // خصم صفر
                'image'               => 'products/laptop-standard/cover.jpg',
                'status'              => 1,
            ]);
        });

        // Schema::enableForeignKeyConstraints();
    }
}
