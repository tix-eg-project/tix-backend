<?php

// namespace App\Services\Order;

// use App\Models\Admin;
// use App\Models\Cart;
// use App\Models\CartItem;
// use App\Models\Coupon;
// use App\Models\Order;
// use App\Models\OrderItem;
// use App\Models\PaymentMethod;
// use App\Models\Product;
// use App\Models\ShippingZone;
// use App\Models\UserContact;
// use App\Notifications\DashboardNotification;
// use App\Services\Cart\CartSummaryService;
// use DomainException;
// use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\Log;

// class OrderService
// {
//     public function __construct(protected CartSummaryService $summary) {}

//     protected function currentCart(): Cart
//     {
//         $cart = Cart::where('user_id', (int)Auth::id())->where('status', 0)->first();
//         if (!$cart) throw new DomainException(__('messages.cart.empty'));
//         return $cart;
//     }

//     public function create(): Order
//     {
//         // dd('dha');
//         $cart = $this->currentCart();
//         $calc = $this->summary->summary();

//         $subtotal     = (float)($calc['subtotal'] ?? 0);
//         $shipping     = (float)($calc['shipping_zone']['price'] ?? 0);
//         $discount     = (float)($calc['discount'] ?? 0);
//         $total        = (float)($calc['total'] ?? max(0, round($subtotal + $shipping - $discount, 2)));

//         $zoneId       = $cart->shipping_zone_id ?: null;
//         $zoneName     = null;
//         if ($zoneId) {
//             $z = ShippingZone::find($zoneId);
//             $zoneName = $z?->name_text ?? null;
//         }

//         $pmId         = $cart->payment_method_id ?: null;
//         $pmName       = null;
//         if ($pmId) {
//             $pm = PaymentMethod::find($pmId);
//             $pmName = $pm?->name_text ?? null;
//         }

//         $coupon       = $calc['coupon'] ?? null;
//         $couponCode   = $coupon['code']   ?? null;
//         $couponType   = $coupon['type']   ?? null;
//         $couponValue  = isset($coupon['value'])  ? (float)$coupon['value']  : null;
//         $couponAmount = isset($coupon['amount']) ? (float)$coupon['amount'] : 0.0;

//         $contact = UserContact::where('user_id', Auth::id())->first();
//         if (!$contact) throw new DomainException(__('messages.user_contact.required'));

//         // تحميل الـ variant items مع الـ cart items
//         $items = CartItem::with(['product', 'variantItem'])->where('cart_id', $cart->id)->get();
//         if ($items->isEmpty()) throw new DomainException(__('messages.cart.empty'));

//         $productIds = $items->pluck('product_id')->all();

//         return DB::transaction(function () use ($cart, $items, $productIds, $subtotal, $shipping, $discount, $total, $zoneId, $zoneName, $pmId, $pmName, $couponCode, $couponType, $couponValue, $couponAmount, $contact) {

//             $products = Product::whereIn('id', $productIds)->lockForUpdate()->get()->keyBy('id');

//             foreach ($items as $it) {
//                 $p = $products[$it->product_id] ?? null;
//                 if (!$p) throw new DomainException(__('messages.product.not_found'));
//                 if ((int)$p->quantity < (int)$it->quantity) {
//                     throw new DomainException(__('messages.product.out_of_stock'));
//                 }
//             }

//             $order = Order::create([
//                 'user_id'             => $cart->user_id,
//                 'cart_id'             => $cart->id,
//                 'subtotal'            => $subtotal,
//                 'shipping_price'      => $shipping,
//                 'discount'            => $couponAmount,
//                 'total'               => $total,
//                 'shipping_zone_id'    => $zoneId,
//                 'shipping_zone_name'  => $zoneName,
//                 'payment_method_id'   => $pmId,
//                 'payment_method_name' => $pmName,
//                 'coupon_code'         => $couponCode,
//                 'coupon_type'         => $couponType,
//                 'coupon_value'        => $couponValue,
//                 'coupon_amount'       => $couponAmount,
//                 'contact_address'     => $contact->address,
//                 'contact_phone'       => $contact->phone,
//                 'order_note'          => $contact->order_note,
//                 'status'              => 'placed',
//                 'payment_status'      => 'pending',
//             ]);
//             foreach ($items as $it) {
//                 $p = $products[$it->product_id];
//                 $img = is_array($p->image_urls ?? null) ? ($p->image_urls[0] ?? null) : null;

//                 // حفظ الـ variant item ID في OrderItem
//                 OrderItem::create([
//                     'order_id'                 => $order->id,
//                     'product_id'               => $p->id,
//                     'product_variant_item_id'  => $it->product_variant_item_id,
//                     'product_name'             => $p->name_text ?? $p->name,
//                     'product_image'            => $img,
//                     'price_before'             => (float)$it->unit_price_before,
//                     'price_after'              => (float)$it->unit_price_after,
//                     'discount_amount'          => max(0, round(((float)$it->unit_price_before - (float)$it->unit_price_after), 2)),
//                     'quantity'                 => (int)$it->quantity,
//                     'vendor_id'                => $p->vendor_id,
//                 ]);

//                 $p->decrement('quantity', (int)$it->quantity);
//             }

//             $cart->status = 1;
//             $cart->save();
//             // لا نحذف الـ CartItems علشان نفضل محتفظين بالسجل التاريخي

//             if ($couponCode) {
//                 $c = Coupon::where('code', $couponCode)->first();
//                 if ($c && isset($c->used_count)) $c->increment('used_count');
//             }


//             $admins = Admin::get();
//             //dd($admins->count());
//             foreach ($admins as $admin) {
//                 Log::info('Sending notification to admin ID: ' . $admin->id);
//                 $admin->notify(new DashboardNotification(__('messages.new_order_message', ['name' => $order->user->name])));
//             }


//             return $order;
//         });
//     }
// }
