<?php

namespace App\Services\Dashboard\Variant;

use App\Models\Product;
use App\Models\ProductVariantItem;

class ProductVariantItemService
{
    public function create(Product $product, array $data)
    {
        // مفيش داعي لـ unset لأن product_id مش موجود في الـ data
        return $product->variantItems()->create($data);
    }

    public function update(ProductVariantItem $item, array $data)
    {
        $item->fill($data)->save();
        return $item;
    }

    public function delete(ProductVariantItem $item)
    {
        $item->delete();
    }
}
