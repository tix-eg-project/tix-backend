<?php

namespace App\Services;

use App\Enums\AmountType;
use App\Models\Favorite;
use Illuminate\Support\Facades\Auth;

class FavoriteService
{
    public function toggle($productId)
    {
        $user = Auth::user();

        $exists = Favorite::where('user_id', $user->id)
            ->where('product_id', $productId)
            ->first();

        if ($exists) {
            $exists->delete();
            return ['removed' => true];
        }

        Favorite::create([
            'user_id'    => $user->id,
            'product_id' => $productId,
        ]);

        return ['added' => true];
    }

    public function list()
    {
        $user = Auth::user();

        return Favorite::where('user_id', $user->id)
            ->with('product')
            ->get()
            ->map(function ($fav) {
                $product = $fav->product;

                if (!$product) {
                    return null;
                }

                $price    = (float) ($product->price ?? 0);
                $discount = (float) ($product->discount ?? 0);
                $type     = (int)   ($product->discount_type ?? AmountType::fixed);

                if ($discount > 0 && $price > 0) {
                    if ($type === AmountType::percent) {
                        $discount_pct = max(0.0, min(100.0, $discount));
                        $price_after  = round($price * (1 - ($discount_pct / 100)), 2);
                    } else {
                        $effective   = min($discount, $price);
                        $discount_pct = round(($effective / $price) * 100, 2);
                        $price_after  = round($price - $effective, 2);
                    }
                } else {
                    $discount_pct = 0.0;
                    $price_after  = $price;
                }

                return [
                    'id'                 => $product->id,
                    'name'               => $product->name_text ?? $product->name,
                    'short_description'  => $product->short_description_text ?? null,

                    'price_before'       => $price,
                    'price_after'        => $price_after,
                    'discount'           => $discount_pct,

                    'images'             => $product->image_urls ?? [],
                ];
            })
            ->filter()
            ->values();
    }

    public function remove($productId)
    {
        $user = Auth::user();

        Favorite::where('user_id', $user->id)
            ->where('product_id', $productId)
            ->delete();
    }

    public function clearAll()
    {
        $user = Auth::user();

        Favorite::where('user_id', $user->id)->delete();
    }
}
