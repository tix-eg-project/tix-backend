<?php

namespace App\Services\Order;

use App\Models\Order;
use App\Models\ProductVariant;
use App\Models\ProductVariantValue;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class UserOrderService
{

    public function list(): LengthAwarePaginator
    {
        $userId = (int) Auth::id();

        $orders = Order::query()
            ->select([
                'id',
                'user_id',
                'status',
                'payment_status',
                'total',
                'shipping_zone_name',
                // ---------------- [VSOFT] هنجيب أعمدة المدينة لعرضها ----------------
                'shipping_vsoft_city_id',    // [VSOFT]
                'shipping_vsoft_city_name',  // [VSOFT]
                // --------------------------------------------------------------------
                'created_at',
                'delivered_at'
            ])
            ->withExists([
                'returnRequests as has_user_return' => fn($q) => $q->where('user_id', $userId)
            ])
            ->with(['items' => function ($q) use ($userId) {
                $q->select([
                    'id',
                    'order_id',
                    'product_id',
                    'product_variant_item_id',
                    'product_name',
                    'product_image',
                    'price_after',
                    'quantity'
                ])
                    ->visible()
                    ->with('variantItem')
                    ->withExists([
                        'returnRequests as has_user_return' => fn($rq) => $rq->where('user_id', $userId)
                    ]);
            }])
            ->where('user_id', $userId)
            ->whereHas('items', function ($q) {
                $q->where('order_items.quantity', '>', 0);
                if (\Illuminate\Support\Facades\Schema::hasColumn('order_items', 'deleted_at')) {
                    $q->whereNull('order_items.deleted_at');
                }
            })
            ->orderByDesc('id')
            ->paginate(10);

        return $orders->through(function (Order $o) {
            return [
                'id'               => $o->id,
                'status'           => $o->status,
                'payment_status'   => $o->payment_status,
                'total'            => (float) $o->total,
                'shipping_zone'    => $o->shipping_zone_name,
                // ------------- [VSOFT] نعرض المدينة المختارة (اختياري) -------------
                'shipping_vsoft_city' => [                                      // [VSOFT]
                    'id'   => $o->shipping_vsoft_city_id,
                    'name' => $o->shipping_vsoft_city_name,
                ],
                // -------------------------------------------------------------------
                'created_at'       => optional($o->created_at)?->toDateTimeString(),
                'delivered_at'     => optional($o->delivered_at)?->toDateTimeString(),
                'has_user_return'  => (bool) $o->has_user_return,
                'items'            => $o->items->map(function ($item) {
                    $variantData = null;
                    if ($item->product_variant_item_id && $item->variantItem) {
                        $variantData = $this->transformVariantItem($item->variantItem);
                    }

                    return [
                        'item_id'         => $item->id,
                        'product_id'      => $item->product_id,
                        'name'            => $item->product_name,
                        'image'           => $item->product_image,
                        'price_after'     => (float) $item->price_after,
                        'quantity'        => (int) $item->quantity,
                        'variant_item'    => $variantData,
                        'has_user_return' => (bool) $item->has_user_return,
                    ];
                })->values()->all(),
            ];
        });
    }

    public function show(int $orderId): array
    {
        $userId = (int) Auth::id();

        $o = Order::query()
            ->select(['*'])
            ->withExists([
                'returnRequests as has_user_return' => fn($q) => $q->where('user_id', $userId)
            ])
            ->with(['items' => function ($q) use ($userId) {
                $q->select([
                    'id',
                    'order_id',
                    'product_id',
                    'product_variant_item_id',
                    'product_name',
                    'product_image',
                    'price_before',
                    'price_after',
                    'discount_amount',
                    'quantity',
                ])
                    ->visible()
                    ->with('variantItem')
                    ->withExists([
                        'returnRequests as has_user_return' => fn($rq) => $rq->where('user_id', $userId)
                    ]);
            }])
            ->where('user_id', $userId)
            ->findOrFail($orderId);

        return [
            'id'             => $o->id,
            'status'         => $o->status,
            'payment_status' => $o->payment_status,
            'subtotal'       => (float) ($o->subtotal ?? 0),
            'shipping_price' => (float) ($o->shipping_price ?? 0),
            'discount'       => (float) ($o->discount ?? 0),
            'total'          => (float) ($o->total ?? 0),
            'shipping_zone'  => $o->shipping_zone_name ?? null,
            // ---------------- [VSOFT] نضيف المدينة في تفاصيل الأوردر ----------------
            'shipping_vsoft_city' => [                                              // [VSOFT]
                'id'   => $o->shipping_vsoft_city_id,
                'name' => $o->shipping_vsoft_city_name,
            ],
            // -----------------------------------------------------------------------
            'payment_method' => $o->payment_method_name ?? null,
            'coupon'         => $o->coupon_code ? [
                'code'   => $o->coupon_code,
                'type'   => $o->coupon_type ?? null,
                'value'  => $o->coupon_value !== null ? (float) $o->coupon_value : null,
                'amount' => (float) ($o->coupon_amount ?? 0),
            ] : null,
            'contact'        => [
                'address'    => $o->contact_address ?? null,
                'phone'      => $o->contact_phone ?? null,
                'order_note' => $o->order_note ?? null,
            ],
            'created_at'     => optional($o->created_at)?->toDateTimeString(),
            'delivered_at'   => optional($o->delivered_at)?->toDateTimeString(),
            'has_user_return' => (bool) $o->has_user_return,
            'items' => $o->items->map(function ($item) {
                $variantData = null;
                if (!empty($item->product_variant_item_id) && $item->variantItem) {
                    $variantData = $this->transformVariantItem($item->variantItem);
                }

                return [
                    'item_id'          => $item->id,
                    'product_id'       => $item->product_id,
                    'name'             => $item->product_name,
                    'image'            => $item->product_image,
                    'price_before'     => (float) ($item->price_before ?? 0),
                    'price_after'      => (float) ($item->price_after ?? 0),
                    'discount_amount'  => (float) ($item->discount_amount ?? 0),
                    'quantity'         => (int) ($item->quantity ?? 0),
                    'variant_item'     => $variantData,
                    'has_user_return'  => (bool) $item->has_user_return,
                ];
            })->values()->all(),
        ];
    }

    protected function transformVariantItem($variantItem): array
    {
        $selections = $variantItem->selections;

        if (is_string($selections)) {
            $selections = json_decode($selections, true) ?? [];
        }

        if (!is_array($selections)) {
            $selections = [];
        }

        $transformedSelections = [];

        foreach ($selections as $selection) {
            $variantId = $selection['product_variant_id'] ?? null;
            $valueId = $selection['product_variant_value_id'] ?? null;

            if (!$variantId || !$valueId) continue;

            $variant = ProductVariant::find($variantId);
            $value = ProductVariantValue::find($valueId);

            if (!$variant || !$value) continue;

            $variantName = $this->getVariantName($variant);
            $valueName = $this->getValueName($value);
            $meta = $this->getValueMeta($value);

            $transformedSelections[] = [
                'variant' => $variantName,
                'value' => $valueName,
                'meta' => $meta
            ];
        }

        return [
            'id' => $variantItem->id,
            'selections' => $transformedSelections,
            'price_before' => (float)$variantItem->price,
            'price_after' => (float)$this->calculateVariantPriceAfter($variantItem)
        ];
    }

    protected function getVariantName($variant): string
    {
        if (!empty($variant->name_text)) {
            return $variant->name_text;
        }

        if (!empty($variant->name)) {
            return is_array($variant->name) ? ($variant->name['ar'] ?? $variant->name['en'] ?? reset($variant->name)) : $variant->name;
        }

        return 'Unknown Variant';
    }

    protected function getValueName($value): string
    {
        if (!empty($value->value_text)) {
            return $value->value_text;
        }

        if (!empty($value->name)) {
            return is_array($value->name) ? ($value->name['ar'] ?? $value->name['en'] ?? reset($value->name)) : $value->name;
        }

        if (!empty($value->value)) {
            if (is_array($value->value)) {
                return $value->value['ar'] ?? $value->value['en'] ?? $value->value['name'] ?? reset($value->value);
            }
            return $value->value;
        }

        return 'Unknown Value';
    }

    protected function getValueMeta($value): ?array
    {
        if (!empty($value->meta)) {
            return is_string($value->meta) ? json_decode($value->meta, true) : $value->meta;
        }

        if (is_array($value->value) && isset($value->value['meta'])) {
            return $value->value['meta'];
        }

        if (is_array($value->value) && isset($value->value['code'])) {
            return ['code' => $value->value['code']];
        }

        return null;
    }

    protected function calculateVariantPriceAfter($variantItem): float
    {
        $product = $variantItem->product;
        $basePrice = (float)$variantItem->price;

        if (!$product) return $basePrice;

        $discount = (float) ($product->discount ?? 0);
        $discountType = $product->discount_type ?? 0;

        if ($discount > 0) {
            if ($discountType === \App\Enums\AmountType::percent) {
                return round($basePrice * (1 - ($discount / 100)), 2);
            } else {
                return round(max($basePrice - $discount, 0), 2);
            }
        }

        return $basePrice;
    }

    public function destroy(int $orderId): void
    {
        Order::where('user_id', Auth::id())->where('id', $orderId)->firstOrFail()->delete();
    }

    public function clear(): void
    {
        Order::where('user_id', Auth::id())->delete();
    }
}
