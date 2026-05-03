<?php

namespace App\Http\Controllers\Api\User\Cart;

use App\Helpers\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Services\Cart\CartSummaryService;
use Illuminate\Http\Request;
// [VSOFT]
use App\Models\VSoftCity;           // [VSOFT]
use App\Models\Cart;                // [VSOFT]
use Illuminate\Support\Facades\Auth; // [VSOFT]

class CartSummaryController extends Controller
{
    public function __construct(protected CartSummaryService $service) {}

    public function summary(Request $r)
    {
        $zoneId = $r->input('zone_id');

        $vsoftCityId   = $r->input('vsoft_city_id');
        $vsoftCityName = null;

        if ($vsoftCityId) {
            $city = VSoftCity::where('vsoft_city_id', (int) $vsoftCityId)->first();
            if ($city) {
                $vsoftCityName = trim(preg_replace('/\s+/', ' ', (string) $city->name)) ?: null;

                if (!$zoneId && $city->shipping_zone_id) {
                    $zoneId = (int) $city->shipping_zone_id;
                }

                $cart = Cart::where('user_id', (int) Auth::id())->where('status', 0)->first();
                if ($cart) {
                    $cart->shipping_vsoft_city_id   = (int) $city->vsoft_city_id;
                    $cart->shipping_vsoft_city_name = $vsoftCityName;
                    $cart->save();
                }
            }
        }

        $coupon = $r->input('coupon') ?? $r->input('coupon_code');

        $data = $this->service->summary(
            $zoneId ? (int)$zoneId : null,
            is_string($coupon) ? trim($coupon) : null
        );

        if ($vsoftCityName && isset($data['shipping_zone']) && is_array($data['shipping_zone'])) {
            $data['shipping_zone']['name'] = (string) $vsoftCityName;
        }

        return ApiResponseHelper::success('messages.cart.summary', $data);
    }

    public function removeCoupon()
    {
        $data = $this->service->removeCoupon();
        return ApiResponseHelper::success('messages.coupon.removed', $data);
    }
}
