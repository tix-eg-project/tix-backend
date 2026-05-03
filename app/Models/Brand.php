<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasTranslatedName;

class Brand extends Model
{
    use HasTranslatedName;
    protected $table = 'brands';


    protected $fillable = ['name'];

    protected $casts = [
        'name' => 'array',
    ];
}
