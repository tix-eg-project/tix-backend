<?php

namespace App\Http\Resources;

use App\Enums\AmountType;
use App\Models\Favorite;
use App\Models\ProductVariant;
use App\Models\ProductVariantValue;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class ProductDetailsResource extends JsonResource
{
    // ==================== helpers (NEW) ====================
    private function normalizeRows(array $rows): array
    {
        return collect($rows)->map(function ($r) {
            return [
                'variant' => trim((string)($r['variant'] ?? '')),
                'value'   => trim((string)($r['value'] ?? '')),
                'meta'    => $r['meta'] ?? null,
            ];
        })->filter(fn($r) => $r['variant'] !== '' && $r['value'] !== '')
            ->values()->all();
    }

    private function buildOptionsKeyFromRows(array $rows): string
    {
        // key موحّد بغض النظر عن ترتيب الإدخال: name:value|...
        $sorted = collect($rows)->sortBy(fn($r) => mb_strtolower($r['variant']))->values();
        return $sorted->map(function ($r) {
            $k = mb_strtolower($r['variant']);
            $v = mb_strtolower($r['value']);
            return "{$k}:{$v}";
        })->implode('|');
    }

    private function pickPrimaryVariant(array $itemsSelections): string
    {
        $allNames = collect($itemsSelections)->flatMap(function ($rows) {
            return collect($rows)->pluck('variant');
        });

        if ($allNames->contains('Color')) {
            return 'Color';
        }

        return $allNames->countBy()->sortDesc()->keys()->first() ?? 'Color';
    }
    // =======================================================

    public function toArray($request)
    {
        $user = auth('sanctum')->user();

        $isFav = false;
        if ($user) {
            $isFav = Favorite::where('user_id', $user->id)
                ->where('product_id', $this->id)
                ->exists();
        }

        $price    = (float) ($this->price ?? 0);
        $discount = (float) ($this->discount ?? 0);
        $type     = (int)   ($this->discount_type ?? 0);

        if ($discount > 0) {
            if ($type === AmountType::percent) {
                $price_after  = round($price * (1 - ($discount / 100)), 2);
                $discount_pct = $discount;
            } else {
                $price_after  = max(round($price - $discount, 2), 0);
                $discount_pct = $price > 0 ? round(($discount / $price) * 100, 2) : 0.0;
            }
        } else {
            $price_after  = $price;
            $discount_pct = 0.0;
        }

        $this->loadMissing(['subcategory.category', 'brand', 'variantItems', 'vendor']);

        $items = $this->variantItems?->where('is_active', 1)->values() ?? collect();

        $variantItems = [];
        if ($items->isNotEmpty()) {
            $allSelections = $items->pluck('selections')->filter()->flatten(1);
            $variantIds = collect($allSelections)->pluck('product_variant_id')->unique()->values();
            $valueIds   = collect($allSelections)->pluck('product_variant_value_id')->unique()->values();

            $variantMap = ProductVariant::whereIn('id', $variantIds)->get()
                ->mapWithKeys(function ($v) {
                    $name = method_exists($v, 'getNameTextAttribute')
                        ? $v->name_text
                        : ($v->name['ar'] ?? ($v->name['en'] ?? (is_array($v->name) ? reset($v->name) : $v->name)));
                    return [$v->id => $name];
                });

            $valueMap = ProductVariantValue::whereIn('id', $valueIds)->get()
                ->mapWithKeys(function ($vv) {
                    $name = method_exists($vv, 'getNameTextAttribute')
                        ? $vv->name_text
                        : ($vv->name['ar'] ?? ($vv->name['en'] ?? (is_array($vv->name) ? reset($vv->name) : $vv->name)));
                    return [$vv->id => [
                        'name'       => $name,
                        'variant_id' => $vv->product_variants_id,
                        'meta'       => $vv->meta,
                    ]];
                });

            $variantItems = $items->map(function ($item) use ($variantMap, $valueMap, $discount, $type) {
                $rows = [];
                foreach ((array)($item->selections ?? []) as $sel) {
                    $pvId  = (int)($sel['product_variant_id'] ?? 0);
                    $pvvId = (int)($sel['product_variant_value_id'] ?? 0);

                    $variantName = $variantMap[$pvId] ?? null;
                    $val         = $valueMap[$pvvId] ?? null;

                    if ($variantName && $val && ($val['variant_id'] ?? null) == $pvId) {
                        $row = [
                            'variant' => $variantName,
                            'value'   => $val['name'] ?? null,
                        ];
                        if (!is_null($val['meta'] ?? null)) {
                            $row['meta'] = $val['meta'];
                        }
                        $rows[] = $row;
                    }
                }

                $base  = (float) $item->price;
                $after = $base;
                if ($discount > 0) {
                    if ($type === AmountType::percent) {
                        $after = round($base * (1 - ($discount / 100)), 2);
                    } else {
                        $after = max(round($base - $discount, 2), 0);
                    }
                }

                $rows = $this->normalizeRows($rows); // NEW
                $key  = $this->buildOptionsKeyFromRows($rows); // NEW

                $attrs = [];
                $meta  = [];
                foreach ($rows as $r) {
                    $attrs[$r['variant']] = $r['value'];
                    if (!empty($r['meta'])) $meta[$r['variant']] = $r['meta'];
                }

                return [
                    'id'            => $item->id,
                    'key'           => $key,
                    'selections'    => $rows,
                    'attrs'         => $attrs,
                    'meta'          => $meta,
                    'price_before'  => $base,
                    'price_after'   => $after,
                    'discount'      => $discount,
                    // 'quantity'    => (int) ($item->quantity ?? 0),
                    // 'sku'         => $item->sku,
                    // 'barcode'     => $item->barcode,
                    // 'image'       => $item->image,
                ];
            })->values()->all();
        }


        $itemsSelectionsOnly = array_map(fn($it) => $it['selections'] ?? [], $variantItems);
        $primary = $this->pickPrimaryVariant($itemsSelectionsOnly);

        $groupsMap = [];

        foreach ($variantItems as $it) {
            $primaryValue = $it['attrs'][$primary] ?? '__none__';

            if (!isset($groupsMap[$primaryValue])) {
                $groupsMap[$primaryValue] = [
                    'value' => $primaryValue,
                    'meta'  => $it['meta'][$primary] ?? null,
                    'items' => [],
                ];
            }

            $attrs = $it['attrs'];
            unset($attrs[$primary]);

            $groupsMap[$primaryValue]['items'][] = [
                'id'           => $it['id'],
                'key'          => $it['key'],
                'attrs'        => $attrs,
                'price_before' => $it['price_before'],
                'price_after'  => $it['price_after'],
                'discount'     => $it['discount'],
            ];
        }

        ksort($groupsMap, SORT_NATURAL | SORT_FLAG_CASE);
        $groups = array_values($groupsMap);

        return [
            'id'                => $this->id,
            'name'              => $this->name_text ?? $this->name,
            'short_description' => $this->short_description_text ?? $this->short_description,
            'long_description'  => $this->long_description_text ?? $this->long_description,
            'price_before'      => $price,
            'price_after'       => $price_after,
            'discount'          => $discount_pct,
            'brand'             => optional($this->brand)->name_text ?? optional($this->brand)->name,
            'category'          => optional(optional($this->subcategory)->category)->name_text
                ?? optional(optional($this->subcategory)->category)->name,
            'subcategory'       => optional($this->subcategory)->name_text ?? optional($this->subcategory)->name,
            'is_fav'            => (bool) $isFav,
            'images'            => $this->image_urls ?? [],

            'primary_variant'   => $primary,
            'groups'            => $groups,

            'features' => $this->featureLinesForLocale(app()->getLocale()),
            'faqs'     => $this->faqsForLocale(app()->getLocale()),
            'reviews'  => $this->reviewsPayload(),

            'vendor' => [
                'id'          => optional($this->vendor)->id,
                'store_name'  => optional($this->vendor)->company_name
                    ?: optional($this->vendor)->name, // fallback
                // لو حابب تعرض صورة المتجر:
                // 'image'    => optional($this->vendor && $this->vendor->image)
                //     ? asset($this->vendor->image) : null,
            ],


            // 'variant_items'   => $variantItems,
        ];
    }

    private function featureLinesForLocale(string $locale): array
    {
        $raw = $this->features ?? null;
        if (!is_array($raw)) {
            return [];
        }
        if (isset($raw[$locale]) && is_array($raw[$locale])) {
            return array_values(array_filter(
                array_map(static fn ($x) => trim((string) $x), $raw[$locale]),
                static fn ($x) => $x !== ''
            ));
        }
        $keys = array_keys($raw);
        if ($keys !== [] && is_int($keys[0])) {
            return array_values(array_filter(
                array_map(static fn ($x) => trim((string) $x), $raw),
                static fn ($x) => $x !== ''
            ));
        }

        return [];
    }

    private function faqsForLocale(string $locale): array
    {
        if (!$this->relationLoaded('faqs')) {
            $this->loadMissing('faqs');
        }

        return $this->faqs->map(function ($f) use ($locale) {
            $q = $f->question ?? [];
            $a = $f->answer ?? [];
            $qOut = is_array($q)
                ? (string) ($q[$locale] ?? ($q['ar'] ?? $q['en'] ?? ''))
                : (string) $q;
            $aOut = is_array($a)
                ? (string) ($a[$locale] ?? ($a['ar'] ?? $a['en'] ?? ''))
                : (string) $a;

            return [
                'id' => $f->id,
                'question' => $qOut,
                'answer' => $aOut,
            ];
        })->values()->all();
    }

    private function reviewsPayload(): array
    {
        if (!$this->relationLoaded('reviews')) {
            $this->load([
                'reviews' => fn ($q) => $q->with('user:id,name')->latest()->limit(30),
            ]);
        } else {
            $this->loadMissing('reviews.user');
        }

        $data = $this->reviews->map(function ($r) {
            return [
                'id' => $r->id,
                'rating' => (int) $r->rating,
                'review' => $r->review,
                'user_name' => $r->user->name ?? null,
                'created_at' => $r->created_at?->toIso8601String(),
            ];
        })->values()->all();

        $avg = $this->resource->reviews()->avg('rating');

        return [
            'data' => $data,
            'average_rating' => $avg !== null ? round((float) $avg, 1) : null,
            'count' => (int) $this->resource->reviews()->count(),
        ];
    }
}
