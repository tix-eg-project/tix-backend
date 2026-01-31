<?php

namespace App\Jobs\Shipping;

use App\Models\Order;
use App\Models\VSoftShipment;
use App\Services\Shipping\VSoftShippingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class PushVSoftShipmentDirect implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $shipmentId;

    public function __construct(int $shipmentId)
    {
        $this->shipmentId = $shipmentId;
        $this->onQueue('shipping');
    }

    public function handle(VSoftShippingService $svc): void
    {
        $shipment = VSoftShipment::find($this->shipmentId);
        if (!$shipment) return;

        $order = Order::find($shipment->order_id);
        if (!$order) {
            $shipment->status = 'failed';
            $shipment->last_error = 'Order not found';
            $shipment->save();
            return;
        }

        if ($shipment->status === 'pushed' && !empty($shipment->awb)) {
            Log::info("VSOFT: shipment already pushed (order #{$order->id}, awb={$shipment->awb})");
            return;
        }

        if (!$shipment->vsoft_city_id) {
            $shipment->status = 'failed';
            $shipment->last_error = 'Missing vsoft_city_id';
            $shipment->save();
            return;
        }

        $isReturn = ((int)$shipment->product_id === 12);

        if (empty($shipment->product_id)) {
            $shipment->product_id = $isReturn
                ? 12
                : ((int) env('VSOFT_DEFAULT_PRODUCT_COD', 5)); 
        }

        $cod    = $isReturn ? 0.0 : 0.0; 

        $weight = (float) ($shipment->weight ?? 0.0);
        $pieces = (int) max(1, $shipment->pieces ?? 1);

        $storeCityId  = (int) env('VSOFT_FROM_CITY_ID', 0);
        $storeAddress = (string) env('VSOFT_FROM_ADDRESS', '');
        $storePhone   = (string) env('VSOFT_FROM_PHONE', '');
        $storeContact = (string) env('VSOFT_FROM_CONTACT', 'YourStore');

        $customerName    = $order->user?->name ?? ($order->shipping_name ?? 'Customer');
        $customerAddress = $order->contact_address ?? ($order->shipping_address ?? '');
        $customerPhone   = $order->contact_phone ?? ($order->shipping_phone ?? '');

        $fromCityID = (int) ($order->shipping_vsoft_city_id ?? $shipment->vsoft_city_id);
        $fromAddr   = $customerAddress;
        $fromPhone  = $customerPhone;
        $fromPerson = $customerName;

        $toCityID   = $storeCityId;
        $toAddr     = $storeAddress;
        $toPhone    = $storePhone;
        $toPerson   = $storeContact;

        $payload = [
            'allMustValid' => true,
            'hasAWBs'      => false,
            'shipments'    => [[
                'productID'          => 12,
                'toRef'              => (string) $order->id,
                'fromCityID'         => $fromCityID,
                'toCityID'           => $toCityID,
                'fromAddress'        => $fromAddr,
                'toAddress'          => $toAddr,
                'fromPhone'          => $fromPhone,
                'toPhone'            => $toPhone,
                'fromContactPerson'  => $fromPerson,
                'toConsigneeName'    => $toPerson,
                'toContactPerson'    => $toPerson,
                'cod'                => (float) $cod,    
                'weight'             => (float) $weight,  
                'pieces'             => (int) $pieces,    
                'specialInstuctions' => (string) ($order->order_note ?? ''),
                'contents'           => (string) ($order->items()->pluck('product_name')->filter()->implode(', ') ?: 'Order Items'),
            ]],
        ];

        $shipment->payload_request = $payload;
        $shipment->status = 'pending';
        $shipment->save();

        try {
            $resp = $svc->saveShipments($payload);
            $shipment->payload_response = $resp ?? null;
            $shipment->pushed_at = now();

            $ok = (bool) data_get($resp, 'generalResponse.success', false);
            $awb = data_get($resp, 'shipments.0.awb')
                ?? data_get($resp, 'shipments.0.AWB')
                ?? data_get($resp, 'awb');

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
}
