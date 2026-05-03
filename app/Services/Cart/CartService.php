<?php

namespace App\Services\Cart;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariantItem;
use App\Models\ProductVariant;
use App\Models\ProductVariantValue;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;


class CartService
{
    protected function ok($data = null): array
    {
        return ['status' => true, 'data' => $data];
    }

    protected function fail(string $msgKey, $data = null): array
    {
        return ['status' => false, 'message' => __($msgKey), 'data' => $data];
    }

    protected function currentCart(): Cart
    {
        $userId = (int) Auth::id();
        $cart = Cart::where('user_id', $userId)->where('status', 0)->first();
        if (!$cart) $cart = Cart::create(['user_id' => $userId, 'status' => 0]);
        return $cart;
    }

    public function add(int $productId, int $quantity, ?int $productVariantItemId = null): array
    {
        $cart = $this->currentCart();

        $product = Product::find($productId);
        if (!$product) return $this->fail('messages.products.not_found');

        $pviId = $productVariantItemId;
        if ($pviId === 0) $pviId = null;
        $pviId = is_null($pviId) ? null : (int)$pviId;

        if (!is_null($pviId)) {
            $variantItem = ProductVariantItem::find($pviId);
            if (!$variantItem) return $this->fail('messages.cart.variant_not_found');

            if ((int)$variantItem->product_id !== (int)$product->id) {
                return $this->fail('messages.cart.variant_not_belong_to_product');
            }
            if (isset($variantItem->is_active) && (int)$variantItem->is_active !== 1) {
                return $this->fail('messages.cart.variant_inactive');
            }
            if (isset($variantItem->quantity) && (int)$variantItem->quantity < 1) {
                return $this->fail('messages.cart.variant_out_of_stock');
            }
        }

        $base = is_null($pviId)
            ? (float)($product->price ?? 0)
            : (float)optional(ProductVariantItem::find($pviId))->price;

        [$before, $after, $disc] = $this->snapshotPrice($product, $base);

        $finalQty = max(1, (int)$quantity);

        DB::transaction(function () use ($cart, $productId, $pviId, $finalQty, $before, $after, $disc) {
            $query = CartItem::where('cart_id', $cart->id)
                ->where('product_id', $productId);

            if (is_null($pviId)) {
                $query->whereNull('product_variant_item_id');
            } else {
                $query->where('product_variant_item_id', $pviId);
            }

            $query->delete();

            CartItem::create([
                'cart_id'                 => $cart->id,
                'product_id'              => $productId,
                'product_variant_item_id' => $pviId,
                'quantity'                => $finalQty,
                'unit_price_before'       => $before,
                'unit_price_after'        => $after,
                'unit_discount'           => $disc,
            ]);
        });

        $item = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $productId)
            ->when(
                is_null($pviId),
                fn($q) => $q->whereNull('product_variant_item_id'),
                fn($q) => $q->where('product_variant_item_id', $pviId)
            )
            ->first();

        return $this->ok($this->transformItem($item));
    }



    public function all(): array
    {
        $cart = $this->currentCart();

        $items = CartItem::with(['product', 'variantItem'])
            ->where('cart_id', $cart->id)
            ->get();

        if ($items->isEmpty()) return $this->ok([]);

        $data = $items->map(fn($i) => $this->transformItem($i))->values()->all();
        return $this->ok($data);
    }

    public function update(int $itemId, int $quantity): array
    {
        $cart = $this->currentCart();
        $item = CartItem::where('cart_id', $cart->id)->where('id', $itemId)->first();
        if (!$item) return $this->fail('messages.cart.item_not_found');

        $item->update(['quantity' => max(1, (int)$quantity)]);
        $item->load(['product', 'variantItem']);

        return $this->ok($this->transformItem($item));
    }



    public function remove(int $itemId): array
    {
        $cart = $this->currentCart();
        $item = CartItem::where('cart_id', $cart->id)
            ->where('id', $itemId)
            ->first();

        if (!$item) {
            return $this->fail('messages.cart.item_not_found');
        }

        DB::transaction(function () use ($cart, $item) {
            $item->delete();

            $hasItems = CartItem::where('cart_id', $cart->id)->exists();
            if (!$hasItems) {
                $cart->coupon_id = null;
                $cart->save();
            }
        });

        return $this->ok();
    }

    public function clear(): array
    {
        $cart = $this->currentCart();

        DB::transaction(function () use ($cart) {
            CartItem::where('cart_id', $cart->id)->delete();

            $cart->coupon_id = null;
            $cart->save();
        });

        return $this->ok();
    }


    protected function snapshotPrice(Product $p, ?float $baseOverride = null): array
    {
        $price = $baseOverride !== null ? (float)$baseOverride : (float)($p->price ?? 0);

        $offerVal  = (float) ($p->offer_value ?? $p->offer ?? 0);
        $offerType = $p->offer_type ?? null;
        $offerFrom = $p->offer_starts_at ?? null;
        $offerTo   = $p->offer_ends_at ?? null;

        $offerActive = false;
        if ($offerVal > 0) {
            $offerActive = true;
            if ($offerFrom || $offerTo) {
                $now = Carbon::now();
                if ($offerFrom && $now->lt(Carbon::parse($offerFrom))) $offerActive = false;
                if ($offerTo   && $now->gt(Carbon::parse($offerTo)))   $offerActive = false;
            }
        }

        $after = $price;

        if ($offerActive) {
            $t = is_string($offerType) ? strtolower($offerType) : $offerType;
            $isPercent = is_string($t)
                ? (str_contains($t, 'percent') || str_contains($t, 'percentage'))
                : ($t === 0 || $t === 1);
            if ($isPercent) {
                $after = round($price * (1 - min(100, $offerVal) / 100), 2);
            } elseif ($offerVal > 0) {
                $after = round(max($price - $offerVal, 0), 2);
            }
        } else {
            $discount = (float) ($p->discount ?? 0);
            $dtype    = $p->discount_type ?? 0;

            $isPercent = false;
            if (is_string($dtype))       $isPercent = str_contains(strtolower($dtype), 'percent');
            elseif (is_int($dtype))      $isPercent = in_array($dtype, [0, 1], true);
            elseif (is_object($dtype) && property_exists($dtype, 'value')) {
                $v = $dtype->value;
                $isPercent = is_string($v) ? str_contains(strtolower($v), 'percent') : in_array($v, [0, 1], true);
            }

            if ($discount > 0) {
                $after = $isPercent
                    ? round($price * (1 - min(100, $discount) / 100), 2)
                    : round(max($price - $discount, 0), 2);
            }
        }

        $before = round($price, 2);
        $disc   = round(max(0, $before - $after), 2);
        return [$before, $after, $disc];
    }

    protected function transformItem(CartItem $item): array
    {
        $p = $item->product;
        $images = $p->image_urls ?? [];

        $variantData = null;
        if ($item->product_variant_item_id && $item->relationLoaded('variantItem') && $item->variantItem) {
            $variantData = $this->transformVariantItem($item->variantItem);
        }

        return [
            'id'      => $item->id,
            'product' => [
                'id'            => $p->id,
                'name'          => $p->name_text ?? $p->name,
                'price_before'  => (float)$item->unit_price_before,
                'price_after'   => (float)$item->unit_price_after,
                'discount'      => (float)($item->unit_price_before > 0
                    ? round(100 * ($item->unit_price_before - $item->unit_price_after) / $item->unit_price_before, 2)
                    : 0),
                'images'        => is_array($images) ? array_values($images) : [],
                'variant_item'  => $variantData,
            ],
            'quantity' => (int) $item->quantity,
            // 'total' => (float)($item->unit_price_after * $item->quantity),
        ];
    }

    protected function transformVariantItem(ProductVariantItem $variantItem): array
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

    protected function getVariantName(ProductVariant $variant): string
    {
        if (!empty($variant->name_text)) {
            return $variant->name_text;
        }

        if (!empty($variant->name)) {
            return is_array($variant->name) ? ($variant->name['ar'] ?? $variant->name['en'] ?? reset($variant->name)) : $variant->name;
        }

        return 'Unknown Variant';
    }

    protected function getValueName(ProductVariantValue $value): string
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

    protected function getValueMeta(ProductVariantValue $value): ?array
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

    protected function calculateVariantPriceAfter(ProductVariantItem $variantItem): float
    {
        $product = $variantItem->product;
        $basePrice = (float)$variantItem->price;

        if (!$product) return $basePrice;

        // تطبيق خصم المنتج على سعر الفاريانت
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
}
