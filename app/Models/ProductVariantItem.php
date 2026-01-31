<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariantItem extends Model
{
    protected $table = 'product_variant_items';

    protected $fillable = [
        // يُفضّل عدم تضمين product_id هنا (هيتحدد من العلاقة)
        'selections',
        'price',
        'quantity',
        'is_active',
        // 'barcode','image','sku','options_key' لو محتاجين
    ];

    protected $casts = [
        'selections' => 'array',
        'price'      => 'decimal:2',
        'is_active'  => 'boolean',
        'quantity'   => 'integer',
    ];

    // protected static function booted()
    // {
    //     static::saving(function (self $item) {
    //         $item->options_key = self::buildOptionsKey($item->selections);
    //     });
    // }

    // public static function buildOptionsKey(?array $selections): string
    // {
    //     $pairs = [];
    //     if ($selections) {
    //         foreach ($selections as $s) {
    //             $pv  = (int)($s['product_variant_id'] ?? 0);
    //             $pvv = (int)($s['product_variant_value_id'] ?? 0);
    //             if ($pv && $pvv) $pairs[$pv] = "{$pv}:{$pvv}";
    //         }
    //     }
    //     ksort($pairs, SORT_NUMERIC);
    //     return implode('|', array_values($pairs)); // مثال: "1:2|2:5|3:7"
    // }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class, 'product_variant_item_id');
    }
}
