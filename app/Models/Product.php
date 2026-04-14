<?php

namespace App\Models;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Favorite;
use App\Models\Offer;
use App\Models\ProductVariant;
use App\Models\ProductVariantItem;
use App\Models\ProductVariantValue;
use App\Models\Subcategory;
use App\Models\Vendor;
use App\Traits\HasTranslatedName;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class Product extends Model
{

    use HasTranslatedName;

    protected $table = 'products';

    protected $fillable = [
        'name',
        'short_description',
        'long_description',
        'price',
        'quantity',
        'discount',
        'discount_type',
        'images',
        'category_id',
        'brand_id',
        'vendor_id',
        'subcategory_id',
        'status',
        'features',
    ];


    protected $casts = [
        'name' => 'array',
        'short_description' => 'array',
        'long_description' => 'array',
        'features' => 'array',
        'images'  => 'array',
        'price'    => 'float',
        'discount' => 'float',
        'discount_type'      => 'integer',
    ];


    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class);
    }
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class, 'product_id', 'id');
    }

    public function offers()
    {
        return $this->belongsToMany(Offer::class, 'offer_product');
    }

    // public function favorites()
    // {
    //     return $this->belongsToMany(Favorite::class, 'favorites');
    // }

    // public function comments():HasMany
    // {
    //     return $this->hasMany(Comment::class)
    // }


    public function getIsFavAttribute()
    {
        if (!Auth::check()) {
            return false;
        }

        return Favorite::where('user_id', Auth::id())
            ->where('product_id', $this->product_id)
            ->exists();
    }


    public function getNameTextAttribute()
    {
        $val = $this->getAttribute('name');
        $locale = app()->getLocale();
        return is_array($val) ? ($val[$locale] ?? ($val['en'] ?? reset($val))) : $val;
    }

    public function getShortDescriptionTextAttribute()
    {
        $val = $this->getAttribute('short_description');
        $locale = app()->getLocale();
        return is_array($val) ? ($val[$locale] ?? ($val['en'] ?? reset($val))) : $val;
    }

    /*
     |------------------------------------------------------------------
     | الأسعار والخصم
     |------------------------------------------------------------------
     | ملاحظة: عدّل الدوال دي حسب أعمدة جدولكم.
     | السيناريو الشائع:
     | - price: السعر الأساسي
     | - sale_price: السعر بعد الخصم (nullable)
     | - أو discount_percent / discount_amount
     */
    public function getPriceBeforeAttribute()
    {
        // لو عندك عمود price
        return (float) ($this->attributes['price'] ?? 0);
    }

    public function getPriceAfterAttribute()
    {
        // أولوية: sale_price إن وُجد < price ، غير كدا ارجع price
        $price = (float) ($this->attributes['price'] ?? 0);
        $sale  = isset($this->attributes['sale_price']) ? (float) $this->attributes['sale_price'] : null;

        if ($sale !== null && $sale > 0 && $sale < $price) {
            return $sale;
        }


        if (isset($this->attributes['discount_percent'])) {
            $dp = max(0.0, min(100.0, (float) $this->attributes['discount_percent']));
            return round($price * (1 - $dp / 100), 2);
        }

        // أو discount_amount
        if (isset($this->attributes['discount_amount'])) {
            $da = max(0.0, (float) $this->attributes['discount_amount']);
            return max(0.0, round($price - $da, 2));
        }

        return $price;
    }

    public function getDiscountPercentAttribute()
    {
        $before = (float) $this->price_before;
        $after  = (float) $this->price_after;

        if ($before > 0 && $after < $before) {
            return round((($before - $after) / $before) * 100, 2);
        }
        return 0.0;
    }

    public function getValueTextAttribute()
    {
        $val = $this->value; // array: ['code'=>'S','label'=>['ar'=>'صغير','en'=>'Small'], ...]
        if (!is_array($val)) {
            return $val;
        }

        // جرّب ترجّع الـ label باللغة الحالية لو موجود
        if (isset($val['label'])) {
            $label = $val['label'];
            if (is_array($label)) {
                $locale = app()->getLocale();
                return $label[$locale] ?? ($label['en'] ?? reset($label));
            }
            return $label;
        }

        // كاحتياطي ارجع الكود
        if (isset($val['code'])) {
            return $val['code'];
        }

        // آخر حل: رجّع الـ JSON كله كنص
        return json_encode($val, JSON_UNESCAPED_UNICODE);
    }


    public function getImageUrlsAttribute(): array
    {
        return collect($this->images ?? [])->map(fn($p) => asset($p))->all();
    }
    public function values()
    {
        return $this->hasMany(ProductVariantValue::class, 'product_variants_id');
    }
    public function variantItems()
    {
        return $this->hasMany(ProductVariantItem::class, 'product_id');
    }

    public function faqs(): HasMany
    {
        return $this->hasMany(ProductFaq::class)->orderBy('id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(ProductReview::class)->orderByDesc('id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
