<?php

namespace App\Models;

use App\Traits\HasTranslatedName;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasTranslatedName;

    protected $table = 'cities';

    protected $fillable = [
        'name',
        'country_id'
    ];
    protected $casts = [
        'name' => 'array',
    ];


    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
