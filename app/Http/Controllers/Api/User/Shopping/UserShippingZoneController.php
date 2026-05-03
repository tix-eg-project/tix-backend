<?php

namespace App\Http\Controllers\Api\User\Shopping;

use App\Http\Controllers\Controller;
use App\Models\ShippingZone;
use App\Helpers\ApiResponseHelper;

class UserShippingZoneController extends Controller
{
    public function index()
    {
        $zones = ShippingZone::select('id', 'name', 'price')->get()
            ->map(function ($zone) {
                return [
                    'id'   => $zone->id,
                    'name' => $zone->name,
                    'price' => $zone->price
                ];
            });

        return ApiResponseHelper::success('messages.shipping_zones_list', $zones);
    }
}
