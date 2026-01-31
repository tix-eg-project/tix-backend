<?php

namespace App\Services\Order;

use App\Jobs\Shipping\PushVSoftShipment;
use App\Models\Admin;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\ShippingZone;
use App\Models\UserContact;
use App\Models\Vendor;
use App\Models\VSoftShipment;
use App\Notifications\DashboardNotification;
use App\Services\Cart\CartSummaryService;
use App\Services\OpayService;
use DomainException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;

class CheckoutService
{
    protected CartSummaryService $summaryService;

    private int $PENDING_TTL_MIN = 60;

    public function __construct(CartSummaryService $summaryService)
    {
        $this->summaryService = $summaryService;
    }

    protected function currentCart(): Cart
    {
        $userId = (int) Auth::id();
        $cart = Cart::where('user_id', $userId)->where('status', 0)->first();
        if (!$cart) {
            throw new DomainException(__('messages.cart.empty'));
        }
        return $cart;
    }

    protected function validateCart(Cart $cart): void
    {
        $items = CartItem::with('product')->where('cart_id', $cart->id)->get();

        if ($items->isEmpty()) {
            throw new DomainException(__('messages.cart.empty'));
        }

        foreach ($items as $item) {
            if (!$item->product) {
                throw new DomainException(__('messages.product.not_found', ['id' => $item->product_id]));
            }

            if ((int) $item->product->quantity < (int) $item->quantity) {
                throw new DomainException(__('messages.product.out_of_stock', [
                    'name' => $item->product->name_text ?? $item->product->name
                ]));
            }
        }

        $contact = UserContact::where('user_id', Auth::id())->first();
        if (!$contact || !$contact->address || !$contact->phone) {
            throw new DomainException(__('messages.user_contact.incomplete'));
        }
    }

    public function processCheckout(?int $paymentMethodId = null, ?string $paymentMethodName = null, ?int $shippingZoneId = null): array
    {
        $cart = $this->currentCart();
        $this->validateCart($cart);

        $paymentMethod = $this->resolvePaymentMethod($paymentMethodId, $paymentMethodName);

        $shippingZone = null;
        if ($cart->shipping_zone_id) {
            $shippingZone = ShippingZone::find($cart->shipping_zone_id);
        }
        if (!$shippingZone && $shippingZoneId) {
            $shippingZone = ShippingZone::find($shippingZoneId);
        }
        if (!$shippingZone) {
            throw new DomainException(__('messages.shipping.zone_required'));
        }

        $summaryRaw    = $this->summaryService->summary();
        $subtotal      = (float) ($summaryRaw['subtotal'] ?? 0);
        $discount      = (float) ($summaryRaw['discount'] ?? 0);
        $shippingArr   = is_array($summaryRaw['shipping_zone'] ?? null) ? $summaryRaw['shipping_zone'] : [];
        $shippingPrice = (float) ($shippingArr['price'] ?? ($shippingZone->price ?? 0));
        $total         = (float) ($summaryRaw['total'] ?? max(0, $subtotal - $discount) + $shippingPrice);

        $couponArr = is_array($summaryRaw['coupon'] ?? null) ? $summaryRaw['coupon'] : [];
        $couponData = [
            'code'   => $couponArr['code'] ?? null,
            'type'   => $couponArr['type'] ?? null,
            'value'  => $couponArr['value'] ?? null,
            'amount' => (float) ($couponArr['amount'] ?? 0),
        ];

        $contact = UserContact::where('user_id', Auth::id())->first();

        $isCod = $this->isCashOnDelivery($paymentMethod);

        return DB::transaction(function () use ($cart, $subtotal, $discount, $shippingPrice, $total, $paymentMethod, $shippingZone, $contact, $couponData, $isCod) {
            $order = null;
            if (!$isCod) {
                $order = $this->findReusablePendingOrder($cart);
            }

            if ($order) {
                $order->update([
                    'subtotal'            => $subtotal,
                    'shipping_price'      => $shippingPrice,
                    'discount'            => $discount,
                    'total'               => $total,
                    'payment_method_id'   => $paymentMethod->id,
                    'payment_method_name' => $paymentMethod->name_text ?? $paymentMethod->name,
                    'coupon_code'         => $couponData['code'],
                    'coupon_type'         => $couponData['type'],
                    'coupon_value'        => $couponData['value'],
                    'coupon_amount'       => $couponData['amount'],
                    'contact_address'     => $contact->address,
                    'contact_phone'       => $contact->phone,
                    'order_note'          => $contact->order_note,
                ]);
            } else {
                $order = $this->createOrder(
                    cart: $cart,
                    subtotal: $subtotal,
                    shippingPrice: $shippingPrice,
                    discount: $discount,
                    total: $total,
                    paymentMethod: $paymentMethod,
                    shippingZone: $shippingZone,
                    contact: $contact,
                    coupon: $couponData,
                    isCod: $isCod
                );

                $this->createOrderItems($order, $cart);
            }

            $paymentData = [];
            if ($isCod) {
                $order->payment_status = 'paid';
                $order->status = 'confirmed';
                $order->save();
                $this->finalizePaidOrder($order);
            } else {
                $paymentData = $this->startOrReuseVisaPayment($order, $paymentMethod);
            }

            return [
                'order'             => $order,
                'payment'           => $paymentData,
                'requires_redirect' => !$isCod,
                'redirect_url'      => !$isCod ? ($paymentData['iframe_url'] ?? null) : null,
                'payment_reference' => $paymentData['reference'] ?? ($order->order_number ?? ('ORD-' . $order->id)),
            ];
        });
    }

    protected function resolvePaymentMethod(?int $id, ?string $name): PaymentMethod
    {
        $method = null;

        if ($id) {
            $method = PaymentMethod::find($id);
        }

        if (!$method && $name) {
            $needle = mb_strtolower(trim($name));
            $method = PaymentMethod::get()->first(function ($m) use ($needle) {
                $n = mb_strtolower($m->name_text ?? $m->name);
                return $n === $needle;
            });
        }

        if (!$method) {
            throw new DomainException(__('messages.payment.method_required'));
        }

        return $method;
    }

    protected function isCashOnDelivery(PaymentMethod $paymentMethod): bool
    {
        if ((int)$paymentMethod->id === 1) {
            return true;
        }

        $name = mb_strtolower($paymentMethod->name_text ?? $paymentMethod->name);
        $codNames = [
            'cash on delivery',
            'الدفع عند الاستلام',
            'دفع عند الاستلام',
            'كاش',
            'نقدي',
        ];

        return in_array($name, $codNames, true) || str_contains($name, 'cod');
    }

    protected function createOrder(
        Cart $cart,
        float $subtotal,
        float $shippingPrice,
        float $discount,
        float $total,
        PaymentMethod $paymentMethod,
        ShippingZone $shippingZone,
        UserContact $contact,
        array $coupon,
        bool $isCod
    ): Order {
        $zoneName = $shippingZone->name_text
            ?? (method_exists($shippingZone, 'getTranslation') ? $shippingZone->getTranslation('name', app()->getLocale()) : null)
            ?? (is_array($shippingZone->name) ? ($shippingZone->name[app()->getLocale()] ?? ($shippingZone->name['ar'] ?? $shippingZone->name['en'] ?? reset($shippingZone->name))) : $shippingZone->name);
        $zoneName = is_string($zoneName) ? trim($zoneName) : ($zoneName ?? '');

        return Order::create([
            'user_id' => $cart->user_id,
            'cart_id' => $cart->id,
            'subtotal' => $subtotal,
            'shipping_price' => $shippingPrice,
            'discount' => $discount,
            'total' => $total,
            'shipping_zone_id' => $shippingZone->id,
            'shipping_zone_name' => $zoneName,

            'shipping_vsoft_city_id'   => $cart->shipping_vsoft_city_id ?? null,
            'shipping_vsoft_city_name' => $cart->shipping_vsoft_city_name ?? null,

            'payment_method_id' => $paymentMethod->id,
            'payment_method_name' => $paymentMethod->name_text ?? $paymentMethod->name,
            'coupon_code' => $coupon['code'],
            'coupon_type' => $coupon['type'],
            'coupon_value' => $coupon['value'],
            'coupon_amount' => $coupon['amount'],
            'contact_address' => $contact->address,
            'contact_phone' => $contact->phone,
            'order_note' => $contact->order_note,
            'status' => $isCod ? 'confirmed' : 'pending_payment',
            'payment_status' => 'pending',
        ]);
    }

    protected function createOrderItems(Order $order, Cart $cart): void
    {
        $items = CartItem::with(['product', 'variantItem'])->where('cart_id', $cart->id)->get();

        foreach ($items as $item) {
            $product = $item->product;
            $images = $product->image_urls ?? [];
            $mainImage = is_array($images) && !empty($images) ? $images[0] : null;

            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'product_variant_item_id' => $item->product_variant_item_id,
                'product_name' => $product->name_text ?? $product->name,
                'product_image' => $mainImage,
                'price_before' => (float) $item->unit_price_before,
                'price_after' => (float) $item->unit_price_after,
                'discount_amount' => (float) $item->unit_discount,
                'quantity' => (int) $item->quantity,
                'vendor_id' => $product->vendor_id,
            ]);
        }
    }

    protected function updateInventory(Cart $cart): void
    {
        $items = CartItem::with('product')->where('cart_id', $cart->id)->get();

        foreach ($items as $item) {
            if ($item->product) {
                $item->product->decrement('quantity', (int) $item->quantity);
            }
        }
    }

    protected function processCoupon(Cart $cart): void
    {
        if ($cart->coupon_id) {
            $coupon = Coupon::find($cart->coupon_id);
            if ($coupon) {
                $coupon->increment('used_count');
            }
        }
    }

    protected function clearCart(Cart $cart): void
    {
        CartItem::where('cart_id', $cart->id)->delete();
        $cart->status = 1;
        $cart->save();
    }

    protected function startOrReuseVisaPayment(Order $order, PaymentMethod $paymentMethod): array
    {
        $baseRef = $order->order_number ?? ('ORD-' . $order->id);

        // إن وُجد Payment pending ومعاه cashierUrl صالح → استخدمه فوراً
        $existingWithUrl = Payment::where('order_id', $order->id)
            ->where('status', 'pending')
            ->whereNotNull('payload')
            ->get()
            ->first(function ($p) {
                return !empty(data_get($p->payload, 'cashierUrl'));
            });

        if ($existingWithUrl) {
            return [
                'id'         => $existingWithUrl->id,
                'reference'  => $existingWithUrl->reference,
                'amount'     => $existingWithUrl->amount,
                'currency'   => $existingWithUrl->currency,
                'iframe_url' => data_get($existingWithUrl->payload, 'cashierUrl'),
            ];
        }

        // وإلا: Payment جديد بمرجع فريد (A{n})
        $attempt   = Payment::where('order_id', $order->id)->count() + 1;
        $reference = $baseRef . '-A' . $attempt;

        $payment = Payment::create([
            'order_id'  => $order->id,
            'provider'  => $this->getPaymentProvider($paymentMethod), // 'opay'
            'reference' => $reference,
            'amount'    => $order->total,
            'currency'  => 'EGP',
            'status'    => 'pending',
            'payload'   => null,
        ]);

        $user    = $order->user()->first();
        $contact = UserContact::where('user_id', $order->user_id)->first();

        $pgSession = app(OpayService::class)->initiateCashierPayment([
            'reference'    => $reference,
            'amount_egp'   => (float) $payment->amount,
            'userEmail'    => $user?->email,
            'userId'       => $order->user_id,
            'userPhone'    => $contact?->phone,
            'userName'     => $user?->name,
            'product_name' => 'Order #' . $order->id,
            'product_desc' => 'Checkout payment',
        ]);

        $iframeUrl = data_get($pgSession, 'data.cashierUrl');

        if ($iframeUrl) {
            $payment->payload = $pgSession['data'];
            $payment->save();
        } else {
            Log::warning('OPay: cashierUrl missing on create', ['order_id' => $order->id, 'ref' => $reference, 'resp' => $pgSession]);
        }

        return [
            'id'         => $payment->id,
            'reference'  => $payment->reference,
            'amount'     => $payment->amount,
            'currency'   => $payment->currency,
            'iframe_url' => $iframeUrl,
        ];
    }

    protected function getPaymentProvider(PaymentMethod $paymentMethod): string
    {
        return 'opay';
    }

    protected function generatePaymentReference(): string
    {
        return 'PAY-' . time() . '-' . rand(1000, 9999);
    }

    protected function finalizePaidOrder(Order $order): void
    {
        try {
            if ($order->payment_status !== 'paid') {
                return;
            }

            $cart = null;
            if (!empty($order->cart_id)) {
                $cart = Cart::find($order->cart_id);
            }

            if ($cart) {
                $this->updateInventory($cart);
                $this->processCoupon($cart);
                $this->clearCart($cart);
            }

            if ($order->status !== 'confirmed') {
                $order->status = 'confirmed';
                $order->save();
            }

            DB::afterCommit(function () use ($order) {
                try {
                    $msg = __('messages.new_order_message');

                    foreach (Admin::get() as $admin) {
                        $admin->notify(new DashboardNotification(
                            $msg,
                            route('admin.orders.index')
                        ));
                    }

                    $vendorIds = $order->items()
                        ->whereNotNull('vendor_id')
                        ->pluck('vendor_id')
                        ->unique()
                        ->all();

                    foreach ($vendorIds as $vid) {
                        if ($vendor = Vendor::find($vid)) {
                            $vendor->notify(new DashboardNotification(
                                $msg,
                                route('vendor.orders.index')
                            ));
                        }
                    }
                } catch (\Throwable $e) {
                    Log::warning('Order notifications failed: ' . $e->getMessage());
                }
            });

            try {
                if (!VSoftShipment::where('order_id', $order->id)->exists()) {
                    $pieces = max(1, (int) $order->items()->sum('quantity'));
                    $weight = (float) (env('VSOFT_BASE_WEIGHT_KG', 1) ?: 1);

                    VSoftShipment::create([
                        'order_id'         => $order->id,
                        'vsoft_city_id'    => $order->shipping_vsoft_city_id,
                        'product_id'       => null,
                        'cod'              => $this->isCashOnDelivery(PaymentMethod::find($order->payment_method_id)) ? (float) $order->total : 0,
                        'weight'           => $weight,
                        'pieces'           => $pieces,
                        'shipping_zone_id' => $order->shipping_zone_id,
                        'price_snapshot'   => $order->shipping_price,
                        'status'           => 'pending',
                    ]);
                }

                PushVSoftShipment::dispatch($order->id)->onQueue('shipping');
            } catch (\Throwable $e) {
                Log::warning('VSOFT snapshot/dispatch failed: ' . $e->getMessage());
            }
        } catch (\Throwable $e) {
            Log::error('finalizePaidOrder error: ' . $e->getMessage());
        }
    }

    protected function findReusablePendingOrder(Cart $cart): ?Order
    {
        $cutoff = Carbon::now()->subMinutes($this->PENDING_TTL_MIN);

        return Order::where('user_id', $cart->user_id)
            ->where('cart_id', $cart->id)
            ->where('status', 'pending_payment')
            ->where('payment_status', 'pending')
            ->where('created_at', '>=', $cutoff)
            ->latest('id')
            ->first();
    }
}
