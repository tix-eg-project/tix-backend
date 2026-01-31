<?php

namespace App\Http\Controllers\Web\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Offer;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;

class VendorController extends Controller
{
    public function dashboard()
    {
        $vendorId = auth('vendor')->id();

        // أعداد أساسية
        $productsCount = Product::where('vendor_id', $vendorId)->count();
        $offersCount   = Offer::where('vendor_id', $vendorId)->count();

        // عدد الطلبات التي تحتوي عناصر تخص هذا الفندور
        $ordersCount = Order::whereHas('items', function ($q) use ($vendorId) {
            $q->where('vendor_id', $vendorId)
                ->orWhereHas('product', fn($p) => $p->where('vendor_id', $vendorId));
        })->count();

        // إجمالي مبيعات عناصر التاجر (آخر 30 يوم)
        $revenue30d = (float) (OrderItem::where(function ($q) use ($vendorId) {
            $q->where('vendor_id', $vendorId)
                ->orWhereHas('product', fn($p) => $p->where('vendor_id', $vendorId));
        })
            ->whereHas('order', fn($o) => $o->where('status', '!=', 'canceled'))
            ->whereBetween('created_at', [now()->subDays(30), now()])
            ->select(DB::raw('SUM(price_after * quantity) as total'))
            ->value('total') ?? 0);

        return view('Vendor.dashboard', compact(
            'productsCount',
            'offersCount',
            'ordersCount',
            'revenue30d'
        ));
    }
}
