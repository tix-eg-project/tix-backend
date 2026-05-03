<?php

namespace App\Models;


use App\Models\Category;
use App\Traits\HasTranslatedName;
use Illuminate\Database\Eloquent\Model;

class Subcategory extends Model
{
    protected $table = 'subcategories';
    use  HasTranslatedName;
    protected $fillable = ['category_id', 'name', 'description', 'image'];

    protected $casts = [
        'name' => 'array',
        'description' => 'array',
    ];



    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
