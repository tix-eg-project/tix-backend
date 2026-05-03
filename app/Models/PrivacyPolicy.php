<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasTranslatedName;

class PrivacyPolicy extends Model
{
    use HasTranslatedName;
    protected $fillable = ['content_ar', 'content_en'];
}
