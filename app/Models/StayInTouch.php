<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class StayInTouch extends Model
{
    use HasTranslations;

    protected $table = 'stay_in_touch';

    protected $fillable = [
        'address',
        'phones',
        'work_hours',
        'map_link',
        'web_link',
        'whatsapp_link',
        'email',
    ];

    public $translatable = [
        'address',
        'work_hours',
        'web_link',
    ];

    protected $casts = [
        'phones' => 'array',
        'email'  => 'array',

    ];

    protected $appends = ['map_embed_url'];

    public function getMapEmbedUrlAttribute(): ?string
    {
        $url = $this->map_link;

        if (!$url) {
            return null;
        }

        if (str_contains($url, 'output=embed') || str_contains($url, '/embed?')) {
            return $url;
        }

        if (preg_match('/[?&]q=([\d\.\-]+),([\d\.\-]+)/', $url, $m)) {
            [$all, $lat, $lng] = $m;
            return "https://www.google.com/maps?q={$lat},{$lng}&output=embed";
        }

        return $url;
    }
}
