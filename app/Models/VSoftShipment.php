<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VSoftShipment extends Model
{
    // [FIX] Laravel كان بيفترض v_soft_shipments — إحنا عايزين vsoft_shipments
    protected $table = 'vsoft_shipments'; // ← سطر مهم

    protected $fillable = [
        'order_id',
        'vsoft_city_id',
        'product_id',
        'cod',
        'weight',
        'pieces',
        'shipping_zone_id',
        'price_snapshot',
        'awb',
        'status',
        'pushed_at',
        'retries',
        'last_error',
        'payload_request',
        'payload_response'
    ];

    protected $casts = [
        'payload_request'  => 'array',
        'payload_response' => 'array',
        'pushed_at'        => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
