<?php

namespace App\Services\Cart;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\ShippingZone;
use App\Models\VSoftCity; // [VSOFT]
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class CartSummaryService
{
    protected function currentCart(): Cart
    {
        $userId = (int) Auth::id();
        $cart = Cart::where('user_id', $userId)->where('status', 0)->first();
        if (!$cart) $cart = Cart::create(['user_id' => $userId, 'status' => 0]);
        return $cart;
    }

    protected function subtotal(Cart $cart): float
    {
        $sum = 0.0;
        foreach (CartItem::where('cart_id', $cart->id)->get() as $it) {
            $sum += (float)$it->unit_price_after * (int)$it->quantity;
        }
        return round($sum, 2);
    }

    protected function resolveCoupon(?string $code): ?Coupon
    {
        if (!$code) return null;
        return Coupon::whereRaw('LOWER(code)=?', [mb_strtolower($code)])->first();
    }

    protected function validCoupon(?Coupon $c): bool
    {
        if (!$c) return false;
        if (!$c->is_active) return false;
        $now = Carbon::now();
        if ($c->starts_at && $now->lt($c->starts_at)) return false;
        if ($c->ends_at && $now->gt($c->ends_at)) return false;
        if ($c->max_uses && $c->used_count >= $c->max_uses) return false;
        return true;
    }

    public function summary(?int $zoneId = null, ?string $couponCode = null): array
    {
        $cart = $this->currentCart();

        if ($zoneId) {
            $zone = ShippingZone::findOrFail($zoneId);
            $cart->shipping_zone_id = $zone->id;
            $cart->save();
        }

        if ($couponCode !== null && $couponCode !== '') {
            $c = $this->resolveCoupon($couponCode);
            if (!$this->validCoupon($c)) throw new \DomainException(__('messages.coupon.invalid'));
            $cart->coupon_id = $c->id;
            $cart->save();
        }

        $subtotal = $this->subtotal($cart);

        $shipping = 0.0;
        $zoneData = null;
        if ($cart->shipping_zone_id) {
            $z = ShippingZone::find($cart->shipping_zone_id);
            if ($z) {
                $shipping = (float) $z->price;

                $zoneName = $z->name_text ?? $z->getTranslation('name', app()->getLocale());


                $cityName = null;
                if (!empty($cart->shipping_vsoft_city_name)) {
                    $cityName = trim(preg_replace('/\s+/', ' ', (string) $cart->shipping_vsoft_city_name));
                } elseif (!empty($cart->shipping_vsoft_city_id)) {
                    $vc = VSoftCity::where('vsoft_city_id', (int) $cart->shipping_vsoft_city_id)->first();
                    if ($vc) {
                        $cityName = trim(preg_replace('/\s+/', ' ', (string) $vc->name));
                    }
                }
                if ($cityName) {
                    $zoneName = $cityName;
                }

                $zoneData = [
                    'name'  => $zoneName,
                    'price' => (float) $z->price,
                ];
            } else {
                $cart->shipping_zone_id = null;
                $cart->save();
            }
        }

        $couponData = null;
        $discount = 0.0;
        if ($cart->coupon_id) {
            $c = Coupon::find($cart->coupon_id);
            if ($this->validCoupon($c)) {
                if ($c->discount_type === 'percent') {
                    $discount = round($subtotal * min(100, (float)$c->discount_value) / 100, 2);
                } else {
                    $discount = min($subtotal, round((float)$c->discount_value, 2));
                }
                $couponData = ['code' => $c->code, 'type' => $c->discount_type, 'value' => (float)$c->discount_value, 'amount' => $discount];
            } else {
                $cart->coupon_id = null;
                $cart->save();
            }
        }

        $total = max(0, round($subtotal + $shipping - $discount, 2));

        return [
            'subtotal'      => $subtotal,
            'shipping_zone' => $zoneData,
            'coupon'        => $couponData,
            'discount'      => $discount,
            'total'         => $total,
        ];
    }

    public function removeCoupon(): array
    {
        $cart = $this->currentCart();
        $cart->coupon_id = null;
        $cart->save();
        return $this->summary();
    }
}
