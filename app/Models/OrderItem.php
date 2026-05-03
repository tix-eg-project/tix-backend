<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Schema;

class OrderItem extends Model
{

    protected $fillable = [
        'order_id',
        'product_id',
        'product_variant_item_id',
        'product_name',
        'product_image',
        'price_before',
        'price_after',
        'discount_amount',
        'quantity',
        'vendor_id',
    ];

    protected $casts = [
        'price_before' => 'float',
        'price_after'  => 'float',
        'discount_amount' => 'float',
        'quantity' => 'integer',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function variantItem(): BelongsTo
    {
        return $this->belongsTo(ProductVariantItem::class, 'product_variant_item_id');
    }

    public function returnRequests()
    {
        return $this->hasMany(\App\Models\ReturnRequest::class, 'order_item_id');
    }


    public function scopeVisible($query)
    {
        $table = $query->getModel()->getTable();
        $query->where($table . '.quantity', '>', 0);
        if (Schema::hasColumn($table, 'deleted_at')) {
            $query->whereNull($table . '.deleted_at');
        }
    }
}
