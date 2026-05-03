<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = [
        'user_id',
        'order_id',
        'comment',
        'rating'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function setRatingAttribute($value)
    {
        $this->attributes['rating'] = $value ? $value : 0;
    }
}
