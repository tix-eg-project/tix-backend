<?php

namespace App\Http\Controllers\Api\Shipping;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\VSoftCity;
use App\Models\ShippingZone;
use App\Services\Shipping\ShippingPriceCalculator;

class VSoftController extends Controller
{
    // GET /api/shipping/cities?q=
    public function cities(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $query = VSoftCity::query()
            ->with('shippingZone:id,price')
            ->select(['id', 'vsoft_city_id', 'name', 'vsoft_zone_id', 'shipping_zone_id']);

        if ($q !== '') {
            $query->where(function ($qq) use ($q) {
                $qq->where('name', 'like', "%{$q}%")
                    ->orWhereRaw("JSON_VALID(name) AND JSON_EXTRACT(name, '$.ar') like ?", ["%{$q}%"])
                    ->orWhereRaw("JSON_VALID(name) AND JSON_EXTRACT(name, '$.en') like ?", ["%{$q}%"]);
            });
        }

        $rows   = $query->orderBy('vsoft_city_id', 'asc')->orderBy('name', 'asc')->get();
        $locale = app()->getLocale();

        $out = $rows->map(function (VSoftCity $c) use ($locale) {
            // 1) جرّب الترجمة من resources/lang/{locale}/vsoft_cities.php بالمفتاح (vsoft_city_id)
            $translated = trans("vsoft_cities.{$c->vsoft_city_id}", [], $locale);
            $isMissing  = ($translated === "vsoft_cities.{$c->vsoft_city_id}");
            if ($isMissing) {
                $translated = self::extractNameForLocale($c->name, $locale);
            }

            $name = trim(preg_replace('/\s+/', ' ', (string) $translated));

            // سعر الزون المرتبطة (ثابت، بدون وزن)
            $price = null;
            if ($c->relationLoaded('shippingZone') && $c->shippingZone) {
                $price = (float) $c->shippingZone->price;
            } elseif ($c->shipping_zone_id) {
                if ($z = ShippingZone::find($c->shipping_zone_id)) {
                    $price = (float) $z->price;
                }
            }

            return [
                'id'      => (int) $c->vsoft_city_id,
                'name'    => $name,
                'zone_id' => $c->shipping_zone_id ? (int) $c->shipping_zone_id : null,
                'price'   => $price,
            ];
        })->values();

        return response()->json($out);
    }

    // GET /api/shipping/quote?vsoft_city_id=&weight=
    public function quote(Request $request, ShippingPriceCalculator $calc)
    {
        $validated = $request->validate([
            'vsoft_city_id' => 'required|integer|exists:vsoft_cities,vsoft_city_id',
            'weight'        => 'required|numeric|min:0.1',
        ]);

        $city = VSoftCity::where('vsoft_city_id', $validated['vsoft_city_id'])->firstOrFail();
        abort_if(!$city->shipping_zone_id, 422, 'City is not mapped to any local zone.');

        $zone  = ShippingZone::findOrFail($city->shipping_zone_id);
        $price = $calc->quote($zone, (float)$validated['weight']);

        return response()->json([
            'zone_id'   => $zone->id,
            'zone_name' => $zone->name,
            'price'     => $price,
        ]);
    }

    private static function extractNameForLocale(mixed $raw, string $locale): string
    {
        $isAr = str_starts_with(strtolower($locale), 'ar');

        if (is_array($raw)) {
            $nameAr = $raw['ar'] ?? null;
            $nameEn = $raw['en'] ?? null;
            $pick   = $isAr ? ($nameAr ?? $nameEn) : ($nameEn ?? $nameAr);
            if ($pick) return $pick;
        }

        if (is_string($raw)) {
            $trim = ltrim($raw);
            if ($trim !== '' && ($trim[0] === '{' || $trim[0] === '[')) {
                $arr = json_decode($raw, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($arr)) {
                    $nameAr = $arr['ar'] ?? null;
                    $nameEn = $arr['en'] ?? null;
                    $pick   = $isAr ? ($nameAr ?? $nameEn) : ($nameEn ?? $nameAr);
                    if ($pick) return $pick;
                }
            }
            return $raw;
        }

        return $isAr ? 'مدينة' : 'City';
    }
}
