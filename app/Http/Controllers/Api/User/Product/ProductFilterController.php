<?php

namespace App\Http\Controllers\Api\User\Product;

use App\Enums\AmountType;
use App\Enums\Status;
use App\Helpers\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class ProductFilterController extends Controller
{
    /**
     * GET /api/products/filter
     *
     * باراميترز مدعومة (AND logic):
     * - name | q                : بحث بالاسم/الوصف (AR/EN)
     * - category | category_id[]: فلترة بالأقسام
     * - subcategory | subcategory_id[]: فلترة بالأقسام الفرعية
     * - company | vendor_id[]   : فلترة بالمورد
     * - price_min / price_max   : مدى السعر "بعد الخصم"
     * - weight_min / weight_max : مدى الوزن (إن وُجد عمود weight)
     * - length_min / length_max : مدى الطول (إن وُجد عمود length)
     * - variants[code][]=valueCode | variant_value_ids[] : فلترة بالـvariants
     * - variant_mode=all|any    : افتراضي all
     * - related_to              : منتجات مشابهة لنفس الـsubcategory مع استبعاد المنتج الهدف
     * - sort=newest|price_low|price_high|discount_desc|name_asc|name_desc
     * - per_page                : حجم الصفحة
     */
    public function filter(Request $request)
    {
        // ✅ Validation خفيف (ممكن نفصله في FormRequest لو حبيت)
        $request->validate([
            'name'               => ['nullable', 'string', 'max:200'],
            'q'                  => ['nullable', 'string', 'max:200'],

            'category'           => ['nullable'],
            'category_id'        => ['nullable', 'array'],
            'category_id.*'      => ['integer'],

            'subcategory'        => ['nullable'],
            'subcategory_id'     => ['nullable', 'array'],
            'subcategory_id.*'   => ['integer'],

            'company'            => ['nullable'],
            'vendor_id'          => ['nullable', 'array'],
            'vendor_id.*'        => ['integer'],

            'price_min'          => ['nullable', 'numeric', 'gte:0'],
            'price_max'          => ['nullable', 'numeric', 'gte:0'],
            'weight_min'         => ['nullable', 'numeric', 'gte:0'],
            'weight_max'         => ['nullable', 'numeric', 'gte:0'],
            'length_min'         => ['nullable', 'numeric', 'gte:0'],
            'length_max'         => ['nullable', 'numeric', 'gte:0'],

            'variants'           => ['sometimes', 'array'],
            'variants.*'         => ['sometimes', 'array'],
            'variant_value_ids'  => ['sometimes', 'array'],
            'variant_value_ids.*' => ['integer'],
            'variant_mode'       => ['nullable', 'in:all,any'],

            'related_to'         => ['nullable', 'integer'],

            'sort'               => ['nullable', 'in:newest,price_low,price_high,discount_desc,name_asc,name_desc'],
            'per_page'           => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $perPage = (int) $request->input('per_page', 10);
        $locale  = app()->getLocale();

        /** @var Builder $q */
        $q = Product::query()
            ->with([
                'brand:id,name',
                'subcategory:id,name,category_id',
                'subcategory.category:id,name',
                'vendor:id,name',
            ])
            ->where('status', Status::Active);

        $search = $request->input('name') ?? $request->input('q');
        if ($search) {
            $q->where(function (Builder $w) use ($search) {
                $w->where('name->ar', 'like', "%{$search}%")
                    ->orWhere('name->en', 'like', "%{$search}%")
                    ->orWhere('short_description->ar', 'like', "%{$search}%")
                    ->orWhere('short_description->en', 'like', "%{$search}%");
            });
        }

        // $categoryIds = collect((array)$request->input('category_id', []))
        //     ->merge((array)$request->input('category', []))
        //     ->map(fn($v) => (int)$v)->filter()->unique()->values()->all();
        // if ($categoryIds)   $q->whereIn('category_id', $categoryIds);
        $categoryIds = collect((array)$request->input('category_id', []))
            ->merge((array)$request->input('category', []))
            ->map(fn($v) => (int)$v)->filter()->unique()->values()->all();

        if ($categoryIds) {
            $q->where(function ($w) use ($categoryIds) {
                $w->whereIn('category_id', $categoryIds)
                    ->orWhereHas('subcategory', fn($qq) => $qq->whereIn('category_id', $categoryIds));
            });
        }

        $subcategoryIds = collect((array)$request->input('subcategory_id', []))
            ->merge((array)$request->input('subcategory', []))
            ->map(fn($v) => (int)$v)->filter()->unique()->values()->all();
        if ($subcategoryIds) $q->whereIn('subcategory_id', $subcategoryIds);

        $vendorIds = collect((array)$request->input('vendor_id', []))
            ->merge((array)$request->input('company', []))
            ->map(fn($v) => (int)$v)->filter()->unique()->values()->all();
        if ($vendorIds) $q->whereIn('vendor_id', $vendorIds);

        if ($request->filled('related_to')) {
            $target = Product::select('id', 'subcategory_id')->find($request->integer('related_to'));
            if ($target) {
                $q->where('subcategory_id', $target->subcategory_id)
                    ->where('id', '!=', $target->id);
            }
        }

        $q->select(['products.*'])->selectRaw("
            CASE
              WHEN discount_type = ? THEN ROUND(price * (1 - LEAST(discount,100)/100), 2)
              WHEN discount_type = ? THEN ROUND(GREATEST(price - discount, 0), 2)
              ELSE price
            END AS price_after
        ", [AmountType::percent, AmountType::fixed]);

        if ($request->filled('price_min')) $q->having('price_after', '>=', (float)$request->input('price_min'));
        if ($request->filled('price_max')) $q->having('price_after', '<=', (float)$request->input('price_max'));

        if (Schema::hasColumn('products', 'weight')) {
            if ($request->filled('weight_min')) $q->where('weight', '>=', (float)$request->input('weight_min'));
            if ($request->filled('weight_max')) $q->where('weight', '<=', (float)$request->input('weight_max'));
        }
        if (Schema::hasColumn('products', 'length')) {
            if ($request->filled('length_min')) $q->where('length', '>=', (float)$request->input('length_min'));
            if ($request->filled('length_max')) $q->where('length', '<=', (float)$request->input('length_max'));
        }

        $variantMode     = $request->input('variant_mode', 'all');
        $variantsByCode  = (array) $request->input('variants', []);
        $variantValueIds = (array) $request->input('variant_value_ids', []);

        if (!empty($variantValueIds)) {
            $ids = array_values(array_unique(array_map('intval', $variantValueIds)));
            $q->whereIn('products.id', function ($sub) use ($ids, $variantMode) {
                $sub->from('product_variants as pv')
                    ->join('product_variant_values as pvv', 'pvv.product_variants_id', '=', 'pv.id')
                    ->select('pv.product_id')
                    ->whereIn('pvv.id', $ids)
                    ->groupBy('pv.product_id');
                if ($variantMode === 'all') {
                    $sub->havingRaw('COUNT(DISTINCT pvv.id) >= ?', [count($ids)]);
                }
            });
        }

        if (!empty($variantsByCode)) {
            $q->whereIn('products.id', function ($sub) use ($variantsByCode, $variantMode) {
                $sub->from('product_variants as pv')
                    ->join('product_variant_values as pvv', 'pvv.product_variants_id', '=', 'pv.id')
                    ->select('pv.product_id')
                    ->where(function ($big) use ($variantsByCode) {
                        foreach ($variantsByCode as $nameCode => $valueCodes) {
                            $valueCodes = (array) $valueCodes;
                            $big->orWhere(function ($grp) use ($nameCode, $valueCodes) {
                                $grp->where('pv.name->code', $nameCode)
                                    ->whereIn('pvv.value->code', $valueCodes);
                            });
                        }
                    })
                    ->groupBy('pv.product_id');
                if ($variantMode === 'all') {
                    $sub->havingRaw('COUNT(DISTINCT pv.name->>"$.code") >= ?', [count($variantsByCode)]);
                }
            });
        }

        // ↕️ الترتيب
        switch ($request->input('sort', 'newest')) {
            case 'price_low':
                $q->orderBy('price_after', 'asc');
                break;
            case 'price_high':
                $q->orderBy('price_after', 'desc');
                break;
            case 'discount_desc':
                $q->orderByRaw('(price - price_after) / NULLIF(price, 0) DESC');
                break;
            case 'name_asc':
                $q->orderBy("name->$locale", 'asc');
                break;
            case 'name_desc':
                $q->orderBy("name->$locale", 'desc');
                break;
            default:
                $q->latest('products.id');
        }

        /** @var LengthAwarePaginator $paginator */
        $paginator = $q->paginate($perPage)
            ->through(function (Product $p) {
                $price    = (float) ($p->price ?? 0);
                $discount = (float) ($p->discount ?? 0);
                $type     = (int) ($p->discount_type ?? 0);

                if ($discount > 0) {
                    if ($type === AmountType::percent) {
                        $price_after = round($price * (1 - min($discount, 100) / 100), 2);
                    } elseif ($type === AmountType::fixed) {
                        $price_after = round(max($price - $discount, 0), 2);
                    } else {
                        $price_after = $price;
                    }
                } else {
                    $price_after = $price;
                }

                $discount_pct = $price > 0 ? round(100 * ($price - $price_after) / $price, 2) : 0;

                // الصور من Accessor image_urls
                $images = $p->image_urls ?? [];

                return [
                    'id'             => $p->id,
                    'name'           => $p->name_text ?? $p->name,
                    'images'         => $images,
                    'price_before'          => $price,
                    'discount'       => $discount,
                    'discount_type'  => $type === AmountType::percent ? 'percent' : ($type === AmountType::fixed ? 'fixed' : null),
                    'discount_percent' => $discount_pct,
                    'price_after'    => $price_after,
                    'status'         => $p->status == Status::Active ? __('messages.available') : __('messages.unavailable'),
                    'brand'          => optional($p->brand)->name_text ?? optional($p->brand)->name,
                    'category'       => optional(optional($p->subcategory)->category)->name_text
                        ?? optional(optional($p->subcategory)->category)->name,
                    'subcategory'    => optional($p->subcategory)->name_text ?? optional($p->subcategory)->name,
                    'company'        => optional($p->vendor)->name, // مكافئ productionCompany في مثالك
                ];
            });

        return ApiResponseHelper::paginated($paginator, 'messages.filtered_products');
    }
}
