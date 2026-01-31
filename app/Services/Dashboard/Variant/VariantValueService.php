<?php

namespace App\Services\Dashboard\Variant;

use App\Models\ProductVariant;
use App\Models\ProductVariantValue;

class VariantValueService
{
    public function index(ProductVariant $variant)
    {
        $search = request('search');
        $query  = ProductVariantValue::query()
            ->where('product_variants_id', $variant->id);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name->ar', 'like', '%' . $search . '%')
                    ->orWhere('name->en', 'like', '%' . $search . '%');
            });
        }

        return $query->latest()->paginate(10);
    }

    public function store(ProductVariant $variant, array $data)
    {
        $data['product_variants_id'] = $variant->id;
        ProductVariantValue::query()->create($data);
    }

    public function update(ProductVariantValue $value, array $data)
    {
        $value->update($data);
    }

    public function destroy(ProductVariantValue $value)
    {
        $value->delete();
    }
}
