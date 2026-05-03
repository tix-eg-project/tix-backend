<?php

namespace App\Services\Dashboard\Product;

use App\Enums\Status;
use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductUserService
{
    // نفس العلاقات اللي بتجيبها في الكنترولر
    private array $cardRelations = [
        'brand:id,name',
        'subcategory:id,name,category_id',
        'subcategory.category:id,name',
    ];

    /**
     * كل المنتجات اللي عليها خصم (> 0)
     * يطابق منطق الكنترولر الحالي (بفلتر الحالة).
     */
    public function getDiscounted(int $perPage = 10, float $min = 0): LengthAwarePaginator
    {
        // نفس معالجة الـ Enum/constant للحالة
        $active = Status::Active;
        if (is_object($active) && property_exists($active, 'value')) {
            $active = $active->value;
        }

        return Product::query()
            ->where('status', $active)
            ->whereNotNull('discount')
            ->where('discount', '>', 0)
            ->when($min > 0, fn($q) => $q->where('discount', '>=', $min))
            ->with($this->cardRelations)
            ->select([
                'id',
                'name',
                'short_description',
                'price',
                'discount',
                'discount_type',
                'brand_id',
                'subcategory_id',
                'category_id',
                'images',
                'status',
            ])
            ->orderByDesc('discount')
            ->latest('id')
            ->paginate($perPage);
    }

    /**
     * منتجات عرض معيّن (العرض فعّال + داخل المدة) + فلتر الحالة.
     * يطابق منطق الكنترولر الحالي حرفيًا.
     */
    public function getByOffer(int $offerId, int $perPage = 10): LengthAwarePaginator
    {
        // نفس معالجة الـ Enum/constant للحالة
        $active = Status::Active;
        if (is_object($active) && property_exists($active, 'value')) {
            $active = $active->value;
        }

        return Product::query()
            ->with(array_merge($this->cardRelations, [
                'offers:id,name,amount_type,amount_value,is_active,start_date,end_date',
            ]))
            ->where('status', $active)
            ->whereHas('offers', function ($q) use ($offerId) {
                $q->where('offers.id', $offerId)
                    ->where('is_active', true)
                    ->whereDate('start_date', '<=', now())
                    ->whereDate('end_date', '>=', now());
            })
            ->select([
                'id',
                'name',
                'short_description',
                'price',
                'discount',
                'discount_type',
                'brand_id',
                'subcategory_id',
                'category_id',
                'images',
                'status',
            ])
            ->latest('id')
            ->paginate($perPage);
    }
}
