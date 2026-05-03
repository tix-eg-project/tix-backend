<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductQA extends Model
{
    use HasFactory;

    protected $table = 'product_qas';

    protected $fillable = [
        'product_id',
        'question',
        'answer',
    ];

    protected $casts = [
        'question' => 'array',
        'answer'   => 'array',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Helpers to get translated values manually
     */
    public function getQuestionTextAttribute()
    {
        $locale = app()->getLocale();
        return $this->question[$locale] ?? ($this->question['ar'] ?? ($this->question['en'] ?? ''));
    }

    public function getAnswerTextAttribute()
    {
        $locale = app()->getLocale();
        return $this->answer[$locale] ?? ($this->answer['ar'] ?? ($this->answer['en'] ?? ''));
    }

    public function getTranslation($field, $locale)
    {
        return $this->{$field}[$locale] ?? '';
    }
}
