<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class ProductVariantValue extends Model
{
    use HasTranslations;

    protected $table = 'product_variant_values';

    protected $fillable = ['product_variants_id', 'name', 'meta'];

    public $translatable = ['name'];

    protected $casts = [
        'name' => 'array',
        'meta' => 'array',
    ];

    protected $appends = ['name_text'];

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variants_id');
    }

    public function getNameTextAttribute(): ?string
    {
        $loc = app()->getLocale();
        $val = $this->getTranslation('name', $loc, false);
        if ($val) return $val;

        $arr = (array) $this->getAttribute('name');
        return $arr['ar'] ?? ($arr['en'] ?? (is_array($arr) && $arr ? reset($arr) : null));
    }
}
