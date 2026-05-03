<?php

namespace App\Models;


use App\Traits\HasTranslatedName;


use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use  HasTranslatedName;

    protected $table = 'categories';

    protected $fillable = [
        'name',
        'image',
    ];
    protected $casts = [
        'name' => 'array',
    ];
}
