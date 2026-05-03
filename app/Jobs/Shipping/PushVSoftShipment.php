<?php

namespace App\Jobs\Shipping;

use App\Models\Order;
use App\Models\VSoftShipment;
use App\Services\Shipping\VSoftShippingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class PushVSoftShipment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $orderId; // [CHANGED] رجعنا لـ orderId

    public function __construct(int $orderId) // [CHANGED]
    {
        $this->orderId = $orderId;
        $this->onQueue('shipping');
    }

    public function handle(VSoftShippingService $svc): void
    {
        $order = Order::with(['user', 'items'])->find($this->orderId);
        if (!$order) {
            Log::warning("VSOFT: order not found #{$this->orderId}");
            return;
        }

        $shipment = VSoftShipment::where('order_id', $order->id)
            ->where(function ($q) {
                $q->whereNull('awb')->orWhere('status', 'pending');
            })
            ->orderBy('id')
            ->first();

        if (!$shipment) {
            Log::info("VSOFT: no pending shipment for order #{$order->id}");
            return;
        }

        if ($shipment->status === 'pushed' && !empty($shipment->awb)) {
            Log::info("VSOFT: shipment already pushed (order #{$order->id}, awb={$shipment->awb})");
            return;
        }

        $isReturn = ((int)$shipment->product_id === 12); // [UNCHANGED]

        $isCOD = $this->isCOD($order);
        $productId = (int) ($shipment->product_id ?: (
            $isCOD ? (int) env('VSOFT_DEFAULT_PRODUCT_COD', 5) : (int) env('VSOFT_DEFAULT_PRODUCT_DOMESTIC', 8)
        ));
        $shipment->product_id = $productId; // [UNCHANGED]

        $fromCityId   = (int) env('VSOFT_FROM_CITY_ID', 0);                 // [CHANGED]
        $fromCityName = (string) env('VSOFT_FROM_CITY_NAME', '');           // (اختياري)
        $fromAddress  = (string) env('VSOFT_FROM_ADDRESS', 'Your Store');   // [CHANGED]
        $fromPhone    = (string) env('VSOFT_FROM_PHONE', '');               // [CHANGED]
        $fromContact  = (string) env('VSOFT_FROM_CONTACT', 'YourStore');    // [CHANGED]

        $customerName    = $order->user?->name ?? 'Customer';
        $customerAddress = $order->contact_address ?? '';
        $customerPhone   = $order->contact_phone ?? '';
        $toCityId        = (int) ($shipment->vsoft_city_id ?: $order->shipping_vsoft_city_id);

        if ($isReturn) {

            $payloadFromCityID = $toCityId;
            $payloadToCityID   = $fromCityId;

            $fromAddr   = $customerAddress;
            $fromPh     = $customerPhone;
            $fromPerson = $customerName;

            $toAddr     = $fromAddress;
            $toPh       = $fromPhone;
            $toPerson   = $fromContact;

            $codValue   = 0.0; // [CHANGED] المرتجع دايمًا 0
        } else {
            $payloadFromCityID = $fromCityId;
            $payloadToCityID   = $toCityId;

            $fromAddr   = $fromAddress;
            $fromPh     = $fromPhone;
            $fromPerson = $fromContact;

            $toAddr     = $customerAddress;
            $toPh       = $customerPhone;
            $toPerson   = $customerName;

            $codValue   = $isCOD ? (float) $order->total : 0.0; // [CHANGED]
        }

        $contents = $order->items->pluck('product_name')->filter()->implode(', ');

        $weight = (float) ($shipment->weight ?? 0.0); // [CHANGED] 0.0
        $pieces = (int) max(1, $shipment->pieces ?: $order->items->sum('quantity'));

        $shipmentRow = [
            'awb'                 => '',
            'fromCityID'          => $payloadFromCityID,
            'fromAddress'         => $fromAddr,
            'fromPhone'           => $fromPh,
            'fromContactPerson'   => $fromPerson,

            'toCityID'            => $payloadToCityID,
            'toConsigneeName'     => $toPerson,
            'toAddress'           => $toAddr,
            'toPhone'             => $toPh,
            'toMobile'            => $toPh,

            'toRef'               => (string) $order->id,
            'toContactPerson'     => $toPerson,

            'productID'           => (int) $productId,
            'cod'                 => (float) $codValue,
            'weight'              => $weight,                     // [CHANGED]
            'pieces'              => (int) $pieces,

            'contents'            => $contents,
            'specialInstuctions'  => (string) ($order->order_note ?? ''),

            'awBxAWB'             => true,
            'fromCity'            => $isReturn ? '' : $fromCityName,
            'fromLat'             => 0,
            'fromLng'             => 0,
            'toLat'               => 0,
            'toLng'               => 0,
            'allowToOpenShipment' => true,
        ];

        $payload = [
            'allMustValid' => true,
            'hasAWBs'      => false,
            'shipments'    => [$shipmentRow],
        ];

        $shipment->payload_request = $payload;
        $shipment->status = 'pending';
        $shipment->save();

        try {
            $resp = $svc->saveShipments($payload);

            $shipment->payload_response = $resp ?? null;
            $shipment->pushed_at = now();

            $ok = (bool) data_get($resp, 'generalResponse.success', false);

            $awb = null;
            if (is_array($resp ?? null)) {
                $awb = $resp['shipments'][0]['awb']
                    ?? $resp['shipments'][0]['AWB']
                    ?? $resp['data'][0]['awb']
                    ?? $resp['awb']
                    ?? null;
            }

            if ($ok) {
                if ($awb) {
                    $shipment->awb = (string) $awb;
                    $shipment->status = 'pushed';
                } else {
                    $shipment->status = 'pending';
                }
                $shipment->last_error = null;
            } else {
                $shipment->status = 'failed';
                $shipment->last_error = 'VSoft: generalResponse.success=false';
            }
        } catch (\Throwable $e) {
            $shipment->status = 'failed';
            $shipment->last_error = $e->getMessage();
            $shipment->retries = (int) $shipment->retries + 1;
        }

        $shipment->save();
    }

    private function isCOD(Order $order): bool
    {
        $name = mb_strtolower($order->payment_method_name ?? '');
        return $name === 'cash on delivery'
            || str_contains($name, 'cod')
            || str_contains($name, 'الاستلام')
            || str_contains($name, 'كاش')
            || (int) ($order->payment_method_id ?? 0) === 1;
    }
}
