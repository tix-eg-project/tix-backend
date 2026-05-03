<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'cart_id',
        'subtotal',
        'shipping_price',
        'total',
        'shipping_zone_id',
        'shipping_zone_name',
        'payment_method_id',
        'payment_method_name',
        'coupon_code',
        'coupon_type',
        'coupon_value',
        'coupon_amount',
        'contact_address',
        'contact_phone',
        'order_note',
        'status',
        'payment_status',
        'delivered_at',
'cod_fee',
        'shipping_vsoft_city_id',    // [VSOFT]
        'shipping_vsoft_city_name',
    ];

    protected $casts = ['cod_fee' => 'float',
        'subtotal' => 'float',
        'shipping_price' => 'float',
        'discount' => 'float',
        'total' => 'float',
        'coupon_value' => 'float',
        'coupon_amount' => 'float',
        'delivered_at' => 'datetime',
        'shipping_vsoft_city_id' => 'integer', // [VSOFT]
    ];

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function scopeForVendor($query, $vendorId)
    {
        return $query->whereHas('items', function ($q) use ($vendorId) {
            $q->where('vendor_id', $vendorId)
                ->orWhereHas('product', fn($p) => $p->where('vendor_id', $vendorId));
        });
    }
    
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }
    
    public function returnRequests()
    {
        return $this->hasMany(\App\Models\ReturnRequest::class);
    }
}
