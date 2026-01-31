<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VSoftCity extends Model
{
    // ✅ ثبّت اسم الجدول ليتطابق مع الميجريشن
    protected $table = 'vsoft_cities';

    protected $fillable = [
        'vsoft_city_id',
        'name',
        'vsoft_zone_id',
        'shipping_zone_id',
    ];

    public function shippingZone()
    {
        return $this->belongsTo(ShippingZone::class);
    }
}
