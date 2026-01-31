<?php

namespace App\Services\Order;

use App\Models\Order;
use Illuminate\Support\Carbon;

class AdminOrderService
{
    public function updateStatus(int $orderId, string $status): array
    {
        $o = Order::findOrFail($orderId);
        $o->status = $status;
        $o->save();

        return ['id' => $o->id, 'status' => $o->status];
    }

    public function setDeliveredAt(int $orderId, string $dateTime): array
    {
        $o = Order::findOrFail($orderId);
        $o->delivered_at = Carbon::parse($dateTime);
        $o->status = 'delivered';
        $o->payment_status = $o->payment_status ?: 'paid';
        $o->save();

        return ['id' => $o->id, 'delivered_at' => optional($o->delivered_at)?->toDateTimeString()];
    }
}
