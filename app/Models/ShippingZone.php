<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class ShippingZone extends Model
{
    use HasTranslations;

    protected $fillable = ['name', 'price'];

    public array $translatable = ['name'];

    // public function getNameTextAttribute(): ?string
    // {
    //     return $this->getTranslation('name', app()->getLocale());
    // }

    // public function getNameTextAttribute()
    // {
    //     $name = $this->attributes['name'] ?? null;
    //     $loc  = app()->getLocale();
    //     if (is_null($name)) return '—';
    //     $val = json_decode($name, true);
    //     if (json_last_error() === JSON_ERROR_NONE && is_array($val)) {
    //         return $val[$loc] ?? reset($val);
    //     }
    //     return $name;
    // }
    public function getNameTextAttribute()
    {
        $raw = $this->attributes['name'] ?? null;

        // لو name عبارة عن JSON مخزّن كنص
        if (is_string($raw)) {
            $decoded = json_decode($raw, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $loc = app()->getLocale();
                return $decoded[$loc] ?? reset($decoded) ?? '—';
            }
            return $raw;
        }

        // لو name Array (casted) أو من HasTranslations
        if (is_array($raw)) {
            $loc = app()->getLocale();
            return $raw[$loc] ?? reset($raw) ?? '—';
        }

        return $raw ?? '—';
    }
}
