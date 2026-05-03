<?php

namespace App\Http\Controllers\Api\User\Order;

use App\Helpers\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Services\Order\CheckoutService;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function __construct(protected CheckoutService $service) {}

    public function store(Request $r)
    {
        dd($r->all());
        $r->validate([
            'zone_id' => ['nullable', 'integer', 'min:1'],
            'coupon'  => ['nullable', 'string', 'max:64'],
            'payment_method_name' => ['nullable', 'string', 'max:100'],
        ]);

        $data = $this->service->create($r->only('zone_id', 'coupon', 'payment_method_name'));
        return ApiResponseHelper::success('messages.checkout.created', $data);
    }
}
