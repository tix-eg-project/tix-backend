<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class ProductVariant extends Model
{
    use HasTranslations;

    protected $table = 'product_variants';

    protected $fillable = ['product_id', 'name'];

    public $translatable = ['name'];

    protected $casts = [
        'name' => 'array',
    ];

    protected $appends = ['name_text'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function values()
    {
        return $this->hasMany(ProductVariantValue::class, 'product_variants_id');
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
