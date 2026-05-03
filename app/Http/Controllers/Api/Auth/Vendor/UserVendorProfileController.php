<?php
// app/Http/Controllers/Api/User/Vendor/UserVendorProfileController.php

namespace App\Http\Controllers\Api\Auth\Vendor;

use App\Enums\AmountType;
use App\Helpers\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Vendor;
use Illuminate\Http\JsonResponse;

class UserVendorProfileController extends Controller
{
    public function show(int $id): JsonResponse
    {
        $vendor = Vendor::query()
            ->select('id', 'company_name', 'name', 'description', 'image')
            ->with([
                'products' => function ($q) {
                    $q->select('id', 'vendor_id', 'name', 'price', 'images', 'discount', 'discount_type')
                        // لو عندك حالة للمنتج:
                        // ->where('status', Product::STATUS_ACTIVE ?? 1)
                        ->latest('id');
                },
                'offers' => function ($q) {
                    $q->select('id', 'vendor_id', 'name', 'amount_type', 'amount_value', 'start_date', 'end_date', 'created_at')
                        ->latest('id');
                },
            ])
            ->findOrFail($id);

        $response = [
            'profile' => [
                'id'           => $vendor->id,
                'company_name' => $vendor->company_name,
                'name'         => $vendor->name,
                'description'  => $vendor->description,
                'image'        => $vendor->image ? asset($vendor->image) : asset('vendors/image.png'),
            ],

            'products' => $vendor->products->map(function ($p) {
                $price    = (float) ($p->price ?? 0);
                $discount = (float) ($p->discount ?? 0);
                $type     = (int)   ($p->discount_type ?? 0);

                if ($discount > 0) {
                    if ($type === AmountType::percent) {
                        $price_after  = round($price * (1 - ($discount / 100)), 2);
                        $discount_pct = $discount;
                    } else {
                        // خصم ثابت
                        $price_after  = max(round($price - $discount, 2), 0);
                        $discount_pct = $price > 0 ? round(($discount / $price) * 100, 2) : 0.0;
                    }
                } else {
                    $price_after  = $price;
                    $discount_pct = 0.0;
                }

                return [
                    'id'            => $p->id,
                    'name'          => $p->name,
                    // الحقول المطلوبة:
                    'price_before'  => $price,
                    'price_after'   => $price_after,
                    'discount'      => $discount_pct,   // ← نسبة الخصم مباشرة
                    'images'        => $p->image_urls ?? [],
                    // 'created_at'  => $p->created_at,
                ];
            })->values(),

            'offers' => $vendor->offers->map(function ($o) {
                return [
                    'id'           => $o->id,
                    'name'         => $o->name,
                    'amount_type'  => (int) $o->amount_type,
                    'amount_value' => (float) $o->amount_value,
                    'start_date'   => $o->start_date,
                    'end_date'     => $o->end_date,
                ];
            })->values(),
        ];

        return ApiResponseHelper::success('messages.success', $response);
    }
}
