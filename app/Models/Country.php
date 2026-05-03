<?php

namespace App\Models;

use App\Traits\HasTranslatedName;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use  HasTranslatedName;

    protected $table = 'countries';

    protected $fillable = [
        'name'
    ];
    protected $casts = [
        'name' => 'array',
    ];

    public function cities()
    {
        return $this->hasMany(City::class);
    }
}
