<?php

namespace App\Models;

use App\Traits\HasTranslatedName;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductFeature extends Model
{
    protected $table = 'product_features';

    protected $fillable = [
        'product_id',
        'name',
    ];

    protected $casts = [
        'name' => 'array',
    ];

    public function getNameTextAttribute()
    {
        $val = $this->name;
        $locale = app()->getLocale();
        return is_array($val) ? ($val[$locale] ?? ($val['en'] ?? reset($val))) : $val;
    }

    public function getTranslation(string $field, string $locale): ?string
    {
        $val = $this->{$field};
        return is_array($val) ? ($val[$locale] ?? null) : null;
    }


    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
