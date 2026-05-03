<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CreditNoteItem extends Model
{
    protected $fillable = [
        'credit_note_id',
        'order_item_id',
        'product_id',
        'product_variant_item_id',
        'product_sku',
        'product_name_ar',
        'product_name_en',
        'variant_options',
        'quantity',
        'unit_net',
        'line_total',
    ];

    protected $casts = [
        'variant_options' => 'array',
        'unit_net'  => 'decimal:2',
        'line_total' => 'decimal:2',
    ];

    /* ===== العلاقات ===== */

    public function creditNote()
    {
        return $this->belongsTo(CreditNote::class);
    }

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
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
