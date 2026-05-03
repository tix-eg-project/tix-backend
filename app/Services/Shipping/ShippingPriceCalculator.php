<?php

namespace App\Services\Shipping;

use App\Models\ShippingZone;

class ShippingPriceCalculator
{
    public function quote(ShippingZone $zone, float $weightKg): float|int
    {
        $base = (float) ($zone->price ?? 0);     // السعر الأساسي للزون
        $baseWeight = (float) env('VSOFT_BASE_WEIGHT_KG', 0);
        $overFee    = (float) env('VSOFT_OVERWEIGHT_FEE_PER_KG', 5);

        if ($weightKg <= $baseWeight) return $base;

        $extra = (int) ceil($weightKg - $baseWeight);
        return $base + ($extra * $overFee);
    }
}
