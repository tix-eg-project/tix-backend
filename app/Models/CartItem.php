<?php

namespace App\Models;

use App\Models\Cart;
use App\Models\Product;
use App\Models\ProductVariantItem;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $fillable = [
        'cart_id',
        'product_id',
         'product_variant_item_id', 

        'quantity',
        'unit_price_before',
        'unit_price_after',
        'unit_discount',
    ];
    protected $casts = [
        'unit_price_before' => 'float',
        'unit_price_after' => 'float',
        'unit_discount' => 'float',
    ];
    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function variantItem()
    {
        return $this->belongsTo(ProductVariantItem::class, 'product_variant_item_id');
    }
}
