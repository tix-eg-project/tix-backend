<?php

namespace App\Observers;

use App\Models\Order;
use App\Services\Shipping\CreateReverseShipment;
use Illuminate\Support\Facades\Log;

class OrderObserver
{
    public function updated(Order $order): void
    {
        if ($order->wasChanged('status')) {
            $old = mb_strtolower((string) $order->getOriginal('status'));
            $new = mb_strtolower((string) $order->status);

            if ($new === 'under_return' && $old !== 'under_return') {
                try {
                    // [ADDED] أول ما يدخل under_return نخلق شحنة مرتجع فورًا
                    app(CreateReverseShipment::class)->handle((int)$order->id);
                } catch (\Throwable $e) {
                    Log::warning('OrderObserver reverse create failed: ' . $e->getMessage(), ['order_id' => $order->id]);
                }
            }
        }
    }
}
