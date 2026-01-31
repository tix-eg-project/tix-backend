<?php
// app/Http/Resources/ProductResource.php
namespace App\Http\Resources;

use App\Enums\AmountType;
use App\Models\Favorite;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray($request)
    {

        $user = auth('sanctum')->user();

        $isFav = false;
        if ($user) {
            $isFav = Favorite::where('user_id', $user->id)
                ->where('product_id', $this->id)
                ->exists();
        }



        $price    = (float) ($this->price ?? 0);
        $discount = (float) ($this->discount ?? 0);
        $type     = (int) ($this->discount_type ?? 0);

        if ($discount > 0) {
            if ($type === AmountType::percent) {
                $price_after  = round($price * (1 - ($discount / 100)), 2);
                $discount_pct = $discount;
            } else { // fixed
                $price_after  = max(round($price - $discount, 2), 0);
                $discount_pct = $price > 0 ? round(($discount / $price) * 100, 2) : 0.0;
            }
        } else {
            $price_after  = $price;
            $discount_pct = 0.0;
        }

        return [
            'id'                => (int) $this->id,
            'name'              => $this->name_text,
            'short_description' => $this->short_description_text,

            'price_before'      => $price,
            'price_after'       => $price_after,
            'discount'          => $discount_pct,

            'brand'       => optional($this->brand)->name_text ?? optional($this->brand)->name,
            'category'    => optional(optional($this->subcategory)->category)->name_text
                ?? optional(optional($this->subcategory)->category)->name,
            'subcategory' => optional($this->subcategory)->name_text ?? optional($this->subcategory)->name,

            // الصور جاية من عمود images (JSON) عبر accessor في الموديل: image_urls
            'images' => $this->image_urls ?? [],
            'is_fav'        => $isFav,
        ];
    }
}
