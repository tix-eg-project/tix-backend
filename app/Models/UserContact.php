<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class UserContact extends Model
{
    protected $table = 'user_contacts';

    protected $fillable = [
        'user_id',
        'order_note',
        'address',
        'phone',

    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
