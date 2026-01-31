<?php

namespace App\Services\Dashboard\Product;

use App\Enums\Status;
use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductListService
{

    private array $cardRelations = [
        'brand:id,name',
        'subcategory:id,name,category_id',
        'subcategory.category:id,name',
    ];


    private function activeValue(): int|string
    {
        $active = Status::Active;
        if (is_object($active) && property_exists($active, 'value')) {
            return $active->value; // Backed Enum
        }
        return $active; // constants
    }

    public function list(int $perPage = 15): LengthAwarePaginator
    {
        return Product::query()
            ->with($this->cardRelations)
            ->where('status', $this->activeValue())
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

    public function byCategory(int|string $categoryId, int $perPage = 15): LengthAwarePaginator
    {
        return Product::query()
            ->with($this->cardRelations)
            ->where('status', $this->activeValue())
            ->where(function ($w) use ($categoryId) {
                $w->where('category_id', $categoryId)
                    ->orWhereHas('subcategory', fn($qq) => $qq->where('category_id', $categoryId));
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

    public function bySubcategory(int|string $subcategoryId, int $perPage = 15): LengthAwarePaginator
    {
        return Product::query()
            ->with($this->cardRelations)
            ->where('status', $this->activeValue())
            ->where('subcategory_id', $subcategoryId)
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

    public function byExactDiscount(float $discount, int $perPage = 10): LengthAwarePaginator
    {
        return Product::query()
            ->with($this->cardRelations)
            ->where('status', $this->activeValue())
            ->where('discount', $discount)
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
