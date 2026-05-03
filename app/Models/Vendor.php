<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;

class Vendor extends Authenticatable
{
    use Notifiable, HasApiTokens,HasRoles;

    protected $table = 'vendors';

    protected $fillable = [
        'company_name',
        'description',
        'name',
        'email',
        'phone',
        'password',
        'image',
        'address',
        'postal_code',
        'vodafone_cash',
        'instapay',
        'type_business',
        'category_id',
        'country_id',
        'city_id',
        'status',
        'id_card_front_image',
        'id_card_back_image',
    ];

    protected $guarded = [];


    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function banners()
    {
        return $this->hasMany(Banner::class);
    }
    public function products()
    {
        return $this->hasMany(\App\Models\Product::class, 'vendor_id');
    }

    public function offers()
    {
        return $this->hasMany(\App\Models\Offer::class, 'vendor_id');
    }


    protected $casts = [
        'data' => 'array',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $guard_name = 'vendor';
}
