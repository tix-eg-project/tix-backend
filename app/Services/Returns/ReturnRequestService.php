<?php

namespace App\Services\Returns;

use App\Enums\RefundMethodEnum;
use App\Enums\ReturnReasonEnum;
use App\Enums\ReturnStatusEnum;
use App\Mail\ReturnOrderMail;
use App\Models\DamagedStock;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ReturnRequest;
use App\Services\Shipping\CreateReverseShipment; // [VSOFT-RET]
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
// [VSOFT-RET] نضيف السطر ده عشان نستدعي خدمة إنشاء شحنة المرتجع
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

class ReturnRequestService
{
    public function createForOrderItem(Order $order, array $payload, ?int $actingVendorId = null): ReturnRequest
    {
        return DB::transaction(function () use ($order, $payload, $actingVendorId) {
            $itemId = (int)($payload['order_item_id'] ?? 0);

            $row = DB::table('order_items')
                ->select(['id', 'quantity', 'product_id', 'vendor_id'])
                ->where('order_id', $order->id)
                ->where('id', $itemId)
                ->lockForUpdate()
                ->first();

            if (!$row) {
                throw ValidationException::withMessages([
                    'order_item_id' => [__('messages.returns.item_not_found')],
                ]);
            }

            $vendorId = $row->vendor_id;
            if (empty($vendorId) && !empty($row->product_id)) {
                $vendorId = Product::query()->whereKey($row->product_id)->value('vendor_id');
            }
            if ($actingVendorId !== null && (int)$vendorId !== (int)$actingVendorId) {
                throw ValidationException::withMessages([
                    'vendor' => [__('messages.returns.vendor_forbidden') ?: 'Unauthorized vendor action'],
                ]);
            }

            $deliveredAt = $this->detectDeliveredAt($order);
            if ($deliveredAt && Carbon::parse($deliveredAt)->lt(now()->subDays(14))) {
                throw ValidationException::withMessages([
                    'order_item_id' => [__('messages.returns.window_expired')],
                ]);
            }

            $openExists = ReturnRequest::query()
                ->where('order_item_id', $itemId)
                ->whereNull('deleted_at')
                ->whereIn('status', [
                    ReturnStatusEnum::PendingReview->value,
                    ReturnStatusEnum::UnderReturn->value,
                ])->exists();

            if ($openExists) {
                throw ValidationException::withMessages([
                    'order_item_id' => [__('messages.returns.open_request_exists')],
                ]);
            }

            $alreadyRequested = (int) ReturnRequest::query()
                ->where('order_item_id', $itemId)
                ->whereNull('deleted_at')
                ->where('status', '!=', ReturnStatusEnum::Rejected->value)
                ->sum('quantity');

            $maxReturnable = max(0, (int)$row->quantity - $alreadyRequested);

            $qty = (int) ($payload['quantity'] ?? 0);
            if ($qty < 1) {
                throw ValidationException::withMessages([
                    'quantity' => [__('messages.returns.quantity_min')],
                ]);
            }
            if ($qty > $maxReturnable) {
                throw ValidationException::withMessages([
                    'quantity' => [__('messages.returns.quantity_exceeds', ['max' => $maxReturnable])],
                ]);
            }

            $req = new ReturnRequest([
                'order_id'            => $order->id,
                'order_item_id'       => $itemId,
                'vendor_id'           => $vendorId ?: ($payload['vendor_id'] ?? null),
                'user_id'             => $order->user_id,
                'quantity'            => $qty,
                'status'              => ReturnStatusEnum::PendingReview,
                'reason_code'         => isset($payload['reason_code']) ? ReturnReasonEnum::from((int)$payload['reason_code']) : null,
                'reason_text'         => $payload['reason_text'] ?? null,
                'return_address'      => $payload['return_address'] ?? null,
                'payout_wallet_phone' => $payload['payout_wallet_phone'] ?? null,
                'refund_method'       => isset($payload['refund_method']) ? RefundMethodEnum::from((int)$payload['refund_method']) : null,
            ]);
            $req->save();

            return $req->fresh();
        });
    }


    // public function approveForReturn(ReturnRequest $req, ?Carbon $approvedAt = null, ?int $actingVendorId = null): ReturnRequest
    // {
    //     return DB::transaction(function () use ($req, $approvedAt, $actingVendorId) {
    //         $this->ensureVendorAuthorized($req, $actingVendorId);
    //         $this->guardTransition($req->status, ReturnStatusEnum::UnderReturn);

    //         $req->status      = ReturnStatusEnum::UnderReturn;
    //         $req->approved_at = $approvedAt ?: now();
    //         $req->save();

    //         // DB::afterCommit(function () use ($req) {
    //         //     //$fresh = $req->fresh(['order', 'orderItem', 'user']);
    //         //     // $email = $fresh?->user?->email;
    //         //     // if ($email) {
    //         //     //     Mail::to($email)->send(new ReturnOrderMail($fresh, 'approved_pickup'));
    //         //     // }

    //         //     // [VSOFT-RET] إنشاء شحنة مرتجع (product_id=12) ودفع الجوب بعد نجاح الترنزكشن
    //         //     // try {
    //         //     //     if ($fresh?->order?->id) {
    //         //     //         app(CreateReverseShipment::class)->handle((int)$fresh->order->id); // [VSOFT-RET]
    //         //     //     }
    //         //     // } catch (\Throwable $e) {
    //         //     //     Log::warning('VSOFT-RET create reverse failed: ' . $e->getMessage()); // [VSOFT-RET]
    //         //     // }
    //         // });

    //         return $req->fresh();
    //     });
    // }
    public function approveForReturn(ReturnRequest $req, ?Carbon $approvedAt = null, ?int $actingVendorId = null): ReturnRequest
    {
        return DB::transaction(function () use ($req, $approvedAt, $actingVendorId) {
            $this->ensureVendorAuthorized($req, $actingVendorId);
            $this->guardTransition($req->status, ReturnStatusEnum::UnderReturn);

            $req->status      = ReturnStatusEnum::UnderReturn;
            $req->approved_at = $approvedAt ?: now();
            $req->save();

            // [ADDED] حدّث حالة الأوردر نفسه لـ under_return (مالوش علاقة بالدفع)
            /** @var Order|null $order */
            $order = $req->order()->lockForUpdate()->first();
            if ($order && mb_strtolower((string)$order->status) !== 'under_return') {
                $order->status = 'under_return';        // [ADDED]
                $order->save();                          // [ADDED]
            }

            // [ADDED] بعد الـcommit: أنشئ شحنة مرتجع فورية product=12, COD=0
            DB::afterCommit(function () use ($order, $req) {
                if (!$order) return;

                try {
                    // [ADDED] لو city ناقص، حاول تجيب بديل سريع (اختياري)
                    if (empty($order->shipping_vsoft_city_id)) {
                        $fallbackCityId = (int)($order->shipping_vsoft_city_id
                            ?? $order->customer_city_id
                            ?? 0);
                        if ($fallbackCityId > 0) {
                            $order->shipping_vsoft_city_id = $fallbackCityId;
                            $order->save();
                        }
                    }

                    // [ADDED] قطَع الشحنة = كمية المرتجع الحالية
                    $pieces = max(1, (int)$req->quantity);

                    // [CHANGED] بنبعت القطع والوزن (الوزن = 0.0 عادي)
                    app(CreateReverseShipment::class)->handle(
                        orderId: (int) $order->id,
                        pieces: $pieces,
                        weight: 0.0
                    );
                } catch (\Throwable $e) {
                    Log::warning('VSOFT-RET create reverse failed: ' . $e->getMessage());
                }
            });

            return $req->fresh();
        });
    }


    public function decide(ReturnRequest $req, array $decision, ?int $actingVendorId = null): ReturnRequest
    {
        return DB::transaction(function () use ($req, $decision, $actingVendorId) {
            $this->ensureVendorAuthorized($req, $actingVendorId);

            $type = strtolower((string)($decision['type'] ?? ''));
            if (!in_array($type, ['approved_intact', 'approved_defective', 'rejected'], true)) {
                throw ValidationException::withMessages([
                    'type' => [__('messages.returns.invalid_decision_type')],
                ]);
            }

            if ($type === 'rejected') {
                $this->guardTransition($req->status, ReturnStatusEnum::Rejected);

                $req->status          = ReturnStatusEnum::Rejected;
                $req->received_at     = $decision['received_at'] ?? now();
                $req->refund_subtotal = 0;
                $req->refund_fee      = 0;
                $req->refund_shipping = 0;
                $req->refund_total    = 0;
                $req->save();

                return $req;
            }

            /** @var OrderItem|null $oi */
            $oi = OrderItem::query()->find($req->order_item_id);
            if (!$oi) {
                throw ValidationException::withMessages([
                    'order_item_id' => [__('messages.returns.item_not_found')],
                ]);
            }

            /** @var Order|null $order */
            $order = $req->order()->first();
            if (!$order) {
                throw ValidationException::withMessages([
                    'order_id' => [__('messages.returns.order_not_found')],
                ]);
            }

            $maxForThis = $this->maxReturnableQty($oi, $req->id);
            if ($req->quantity > $maxForThis) {
                throw ValidationException::withMessages([
                    'quantity' => [__('messages.returns.quantity_exceeds', ['max' => $maxForThis])],
                ]);
            }

            $unit    = $this->effectiveUnitPrice($oi, $order);
            $lineNet = round($unit * $req->quantity, 2);

            $feePercent = isset($decision['fee_percent'])
                ? (int)$decision['fee_percent']
                : $this->defaultRestockingPercent($req);

            $fee = round(($lineNet * $feePercent) / 100, 2);
            $shippingAdjust = (float) ($decision['refund_shipping'] ?? 0);
            $total = max(0, round($lineNet - $fee - $shippingAdjust, 2));

            if ($type === 'approved_intact') {
                $this->applyInventoryEffects($req, $oi, $type);
                $req->status = ReturnStatusEnum::ReceivedGood;
            } else {
                $this->recordDamagedStock($req, $oi, (int)$req->quantity);
                $req->status = ReturnStatusEnum::ReceivedDefective;
            }

            $req->received_at     = $decision['received_at'] ?? now();
            $req->refund_subtotal = $lineNet;
            $req->refund_fee      = $fee;
            $req->refund_shipping = $shippingAdjust;
            $req->refund_total    = $total;
            $req->save();

            $this->adjustOrderItemAfterDecision($oi, $req);
            $this->recalculateOrderTotals($order);

            return $req->fresh();
        });
    }

    public function markRefunded(ReturnRequest $req, array $meta = [], ?int $actingVendorId = null): ReturnRequest
    {
        return DB::transaction(function () use ($req, $meta, $actingVendorId) {
            $this->ensureVendorAuthorized($req, $actingVendorId);

            if (!$req->status->canRefund()) {
                throw ValidationException::withMessages([
                    'status' => [__('messages.returns.not_eligible_for_refund')]
                ]);
            }

            if (isset($meta['refund_method'])) {
                $req->refund_method = $meta['refund_method'] instanceof RefundMethodEnum
                    ? $meta['refund_method']
                    : RefundMethodEnum::from((int)$meta['refund_method']);
            }

            $req->refunded_at = $meta['refunded_at'] ?? now();
            $req->status      = ReturnStatusEnum::Refunded;
            $req->save();

            return $req->fresh();
        });
    }

    public function cancel(ReturnRequest $req, ?int $actingVendorId = null): ReturnRequest
    {
        return DB::transaction(function () use ($req, $actingVendorId) {
            $this->ensureVendorAuthorized($req, $actingVendorId);

            if (in_array($req->status, [ReturnStatusEnum::PendingReview, ReturnStatusEnum::UnderReturn], true)) {
                $req->status      = ReturnStatusEnum::Rejected;
                $req->received_at = now();
                $req->save();
                return $req;
            }
            throw ValidationException::withMessages([
                'status' => [__('messages.returns.cannot_cancel_stage')]
            ]);
        });
    }


    protected function ensureVendorAuthorized(ReturnRequest $req, ?int $actingVendorId): void
    {
        if ($actingVendorId === null) return;

        $reqVendor = (int)($req->vendor_id ?? 0);
        if ($reqVendor !== (int)$actingVendorId) {
            throw ValidationException::withMessages([
                'vendor' => [__('messages.returns.vendor_forbidden') ?: 'Unauthorized vendor action'],
            ]);
        }
    }

    public function maxReturnableQty(OrderItem $oi, ?int $excludeReturnRequestId = null): int
    {
        $alreadyRequested = (int) ReturnRequest::query()
            ->where('order_item_id', $oi->id)
            ->whereNull('deleted_at')
            ->where('status', '!=', ReturnStatusEnum::Rejected->value)
            ->when($excludeReturnRequestId, fn($q) => $q->where('id', '!=', $excludeReturnRequestId))
            ->sum('quantity');

        return max(0, (int)$oi->quantity - $alreadyRequested);
    }

    protected function guardTransition(ReturnStatusEnum $from, ReturnStatusEnum $to): void
    {
        $allowed = match ($from) {
            ReturnStatusEnum::PendingReview     => [ReturnStatusEnum::UnderReturn, ReturnStatusEnum::Rejected],
            ReturnStatusEnum::UnderReturn       => [ReturnStatusEnum::ReceivedGood, ReturnStatusEnum::ReceivedDefective, ReturnStatusEnum::Rejected],
            ReturnStatusEnum::ReceivedGood      => [],
            ReturnStatusEnum::ReceivedDefective => [],
            ReturnStatusEnum::Rejected          => [],
        };

        if (!in_array($to, $allowed, true)) {
            throw ValidationException::withMessages([
                'status' => [__('messages.returns.illegal_transition')]
            ]);
        }
    }

    protected function defaultRestockingPercent(ReturnRequest $req): int
    {
        $reason = $req->reason_code;
        if ($reason instanceof ReturnReasonEnum) {
            $cfg = config('returns.restocking_percent_map.' . $reason->value);
            if (!is_null($cfg)) return (int)$cfg;
        }
        return 0;
    }

    protected function effectiveUnitPrice(OrderItem $oi, Order $order): float
    {
        $unit          = $this->inferUnitPrice($oi);
        $lineSubtotal  = $unit * (int)$oi->quantity;
        $orderSubtotal = $this->inferOrderSubtotal($order);
        $orderDiscount = $this->inferOrderDiscount($order);

        if ($orderSubtotal > 0 && $orderDiscount > 0) {
            $share        = $lineSubtotal / $orderSubtotal;
            $lineDiscount = round($orderDiscount * $share, 2);
            $effective    = max(0, round($lineSubtotal - $lineDiscount, 2));
            return round($effective / (int)$oi->quantity, 2);
        }
        return round($unit, 2);
    }

    protected function inferUnitPrice(OrderItem $oi): float
    {
        $candidates = ['unit_price_after', 'unit_price', 'final_price', 'price_after', 'net_price', 'price'];
        foreach ($candidates as $col) {
            if (isset($oi->{$col}) && is_numeric($oi->{$col})) {
                return (float)$oi->{$col};
            }
        }
        if (isset($oi->total) && $oi->quantity > 0) {
            return (float)$oi->total / (int)$oi->quantity;
        }
        return 0.0;
    }

    protected function inferOrderSubtotal(Order $order): float
    {
        foreach (['subtotal', 'items_subtotal', 'subtotal_before_discount', 'sub_total'] as $col) {
            if (isset($order->{$col}) && is_numeric($order->{$col})) {
                return (float)$order->{$col};
            }
        }
        $sum = 0.0;
        foreach ($order->items ?? [] as $it) {
            $sum += $this->inferUnitPrice($it) * (int)$it->quantity;
        }
        return $sum;
    }

    protected function inferOrderDiscount(Order $order): float
    {
        foreach (['discount', 'order_discount', 'discount_total', 'coupon_discount'] as $col) {
            if (isset($order->{$col}) && is_numeric($order->{$col})) {
                return (float)$order->{$col};
            }
        }
        return 0.0;
    }

    protected function inferOrderShipping(Order $order): float
    {
        foreach (['shipping', 'shipping_total', 'shipping_cost', 'delivery_fee', 'shipping_price'] as $col) {
            if (isset($order->{$col}) && is_numeric($order->{$col})) {
                return (float)$order->{$col};
            }
        }
        return 0.0;
    }

    protected function applyInventoryEffects(ReturnRequest $req, OrderItem $oi, string $decisionType): void
    {
        $qty = (int)$req->quantity;

        if ($decisionType === 'approved_intact') {
            if (class_exists(Product::class)) {
                $product = Product::query()->find($oi->product_id);
                if ($product && isset($product->quantity)) {
                    $product->increment('quantity', $qty);
                }
            }
        }
    }

    protected function recordDamagedStock(ReturnRequest $req, OrderItem $oi, int $qty): void
    {
        $payload = [
            'vendor_id'               => $req->vendor_id ?? $oi->vendor_id ?? Product::query()->whereKey($oi->product_id)->value('vendor_id'),
            'product_id'              => $oi->product_id,
            'product_variant_item_id' => $oi->product_variant_item_id ?? null,
            'quantity'                => $qty,
            'reason_code'             => $req->reason_code?->value ?? null,
            'reason_text'             => $req->reason_text,
        ];

        DamagedStock::updateOrCreate(
            ['return_request_id' => $req->id],
            $payload
        );
    }

    private function detectDeliveredAt(Order $order): ?string
    {
        foreach (['delivered_at', 'deliveredAt', 'delivered_date', 'completed_at'] as $col) {
            if (!empty($order->{$col})) return (string)$order->{$col};
        }
        return null;
    }

    protected function adjustOrderItemAfterDecision(OrderItem $oi, ReturnRequest $req): void
    {
        $returnQty = (int) $req->quantity;
        $current   = (int) $oi->quantity;

        if ($returnQty <= 0 || $current <= 0) {
            return;
        }

        if ($returnQty < $current) {
            // مرتجع جزئي → تقليل الكمية
            $oi->quantity = $current - $returnQty;
            $oi->save();
            return;
        }

        $usesSoftDeletes = in_array(\Illuminate\Database\Eloquent\SoftDeletes::class, class_uses_recursive($oi));
        if ($usesSoftDeletes) {
            $oi->delete();
        } else {
            $oi->quantity = 0;
            $oi->save();
        }
    }

    protected function recalculateOrderTotals(Order $order): void
    {
        $itemsQuery = $order->items();

        if (Schema::hasColumn('order_items', 'deleted_at')) {
            $itemsQuery->whereNull('order_items.deleted_at');
        }

        $items = $itemsQuery
            ->where('order_items.quantity', '>', 0)
            ->get();

        $subtotal = 0.0;
        foreach ($items as $it) {
            $unit = $this->inferUnitPrice($it);
            $subtotal += $unit * (int)$it->quantity;
        }
        $subtotal = round($subtotal, 2);

        if ($items->isEmpty()) {
            $discount = 0.0;
            $shipping = 0.0;
            $total    = 0.0;
        } else {
            $discount = round($this->inferOrderDiscount($order), 2);
            $shipping = round($this->inferOrderShipping($order), 2);
            $total    = max(0, round($subtotal - $discount + $shipping, 2));
        }

        $dirty = false;
        foreach (['subtotal', 'items_subtotal', 'subtotal_before_discount', 'sub_total'] as $col) {
            if (array_key_exists($col, $order->getAttributes())) {
                $order->{$col} = $subtotal;
                $dirty = true;
            }
        }
        foreach (['total', 'grand_total', 'order_total'] as $col) {
            if (array_key_exists($col, $order->getAttributes())) {
                $order->{$col} = $total;
                $dirty = true;
            }
        }
        foreach (['items_count', 'items_qty', 'total_items'] as $col) {
            if (array_key_exists($col, $order->getAttributes())) {
                $order->{$col} = (int) $items->sum('quantity');
                $dirty = true;
            }
        }

        if ($dirty) {
            $order->save();
        }
    }
}
