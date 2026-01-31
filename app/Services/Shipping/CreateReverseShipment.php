<?php

namespace App\Services\Shipping;

use App\Jobs\Shipping\PushVSoftShipmentDirect; // [ADDED]
use App\Models\Order;
use App\Models\VSoftShipment;
use Illuminate\Support\Facades\Log;

class CreateReverseShipment
{
    /**
     * أنشئ شحنة مرتجع (product_id=12, COD=0) وأرسلها فورًا بالـshipmentId
     *
     * @param int        $orderId
     * @param int|null   $pieces  [ADDED] عدد القطع للمرتجع (افتراضي 1)
     * @param float|null $weight  [ADDED] وزن الشحنة (افتراضي 0.0)
     */
    public function handle(int $orderId, ?int $pieces = null, ?float $weight = null): bool
    {
        $order = Order::with(['items', 'user'])->find($orderId);
        if (!$order) {
            Log::warning("VSOFT-RET: Order not found #{$orderId}");
            return false;
        }

        if (empty($order->shipping_vsoft_city_id)) {
            Log::warning("VSOFT-RET: shipping_vsoft_city_id is empty for order #{$order->id}");
            return false;
        }

        // [CHANGED] اسمح بشحنة مرتجع جديدة لو السابقة فشلت؛ امنع التكرار لو فيه pending/pushed
        $exists = VSoftShipment::where('order_id', $order->id)
            ->where('product_id', 12)
            ->whereIn('status', ['pending', 'pushed'])
            ->exists();

        if ($exists) {
            Log::info("VSOFT-RET: reverse shipment already exists for order #{$order->id}");
            return false;
        }

        $pieces = $pieces !== null ? max(1, (int)$pieces) : 1;   // [ADDED]
        $weight = $weight !== null ? (float)$weight : 0.0;       // [ADDED]

        $shipment = VSoftShipment::create([
            'order_id'         => $order->id,
            'vsoft_city_id'    => $order->shipping_vsoft_city_id,
            'product_id'       => 12,      // [FIXED]
            'cod'              => 0,       // [FIXED] دايمًا 0 في المرتجع
            'weight'           => $weight, // [CHANGED] 0.0 مسموح
            'pieces'           => $pieces, // [CHANGED] من طلب المرتجع
            'shipping_zone_id' => $order->shipping_zone_id,
            'price_snapshot'   => 0,
            'status'           => 'pending',
        ]);

        // [CHANGED] ابعت الشحنة دي بالذات (من غير اختيار أقدم pending)
        PushVSoftShipmentDirect::dispatch($shipment->id)->onQueue('shipping'); // [CHANGED]

        return true;
    }
}
