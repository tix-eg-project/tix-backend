<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $fillable = [
        'user_id',
        'session_id',
        'status',
        'shipping_zone_id',
        // [VSOFT]
        'shipping_vsoft_city_id',   // [VSOFT]
        'shipping_vsoft_city_name', // [VSOFT]
    ];
    public function items()
    {
        return $this->hasMany(CartItem::class);
    }
    public function scopeActive($q)
    {
        return $q->where('status', 0);
    }
}
