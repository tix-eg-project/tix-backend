<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\HasTranslatedName;

class Banner extends Model
{
    use  HasTranslatedName;

    protected $fillable = [
        'title',
        'description',
        'image',
        'vendor_id',
    ];

    protected $casts = [
        'title' => 'array',
        'description' => 'array',
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }


    protected static array $translatedAttributes = [
        'title',
        'description',
    ];

    public function getTitleAttribute($value)
    {
        $locale = app()->getLocale();
        $decodedValue = json_decode($value, true);

        return is_array($decodedValue) ? $decodedValue[$locale] ?? $decodedValue['en'] : $decodedValue;
    }

    public function getDescriptionAttribute($value)
    {
        $locale = app()->getLocale();
        $decodedValue = json_decode($value, true);

        return is_array($decodedValue) ? $decodedValue[$locale] ?? $decodedValue['en'] : $decodedValue;
    }
}
