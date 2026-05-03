<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DamagedStock extends Model
{
    protected $fillable = [
        'vendor_id',
        'product_id',
        'product_variant_item_id',
        'return_request_id',
        'quantity',
        'reason_code',
        'reason_text',
        // 'warehouse_location',
    ];

    /* ===== العلاقات ===== */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variantItem()
    {
        return $this->belongsTo(ProductVariantItem::class, 'product_variant_item_id');
    }

    public function returnRequest()
    {
        return $this->belongsTo(ReturnRequest::class);
    }
}
