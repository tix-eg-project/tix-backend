<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;


class AboutUs extends Model
{
    use  HasTranslations;

    protected $table = "about_us";
    protected $fillable = [
        'title',
        'description',
        'image',
    ];

    public $translatable = ['title', 'description'];
}
