<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use App\Models\Vendor;

class Offer extends Model
{
    use HasTranslations;

    protected $table = 'offers';

    protected $fillable = [
        'name',
        'amount_type',
        'amount_value',
        'is_active',
        'start_date',
        'end_date',
        'vendor_id',
    ];

    protected $translatable = [
        'name',
    ];

    protected $casts = [
        'is_active'  => 'boolean',
        'start_date' => 'datetime',
        'end_date'   => 'datetime',
        'amount_value' => 'decimal:2',
    ];


    public function vendor()
    {
        return $this->belongsTo(Vendor::class)->withDefault();
    }
    public function products()
    {
        return $this->belongsToMany(Product::class, 'offer_product', 'offer_id', 'product_id')->withTimestamps();
    }
}
