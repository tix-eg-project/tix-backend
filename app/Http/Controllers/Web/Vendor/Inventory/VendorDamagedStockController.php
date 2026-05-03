<?php

namespace App\Http\Controllers\Web\Vendor\Inventory;

use App\Http\Controllers\Controller;
use App\Models\DamagedStock;
use Illuminate\Http\Request;

class VendorDamagedStockController extends Controller
{
    public function index(Request $request)
    {
        $vendorId = (int) auth('vendor')->id();
        $search   = trim((string)$request->input('search', ''));
        $reason   = $request->input('reason_code');

        $items = DamagedStock::query()
            ->with([
                'product',
                'vendor:id,name',
                'variantItem',
                'returnRequest.user:id,name',
            ])
            ->where('vendor_id', $vendorId)
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($qq) use ($search) {
                    if (ctype_digit($search)) {
                        $qq->orWhere('id', (int)$search);
                    }
                    $qq->orWhereHas('product', function ($pq) use ($search) {
                        $pq->where('name', 'like', "%{$search}%");
                            //->orWhere('sku', 'like', "%{$search}%");
                    });
                });
            })
            ->when($reason, fn($q) => $q->where('reason_code', (int)$reason))
            ->latest('id')
            ->paginate(20);

        return view('Vendor.pages.damaged.index', compact('items'));
    }

    public function show($id)
    {
        $vendorId = (int) auth('vendor')->id();

        $item = DamagedStock::query()
            ->with(['product', 'vendor', 'returnRequest.order', 'returnRequest.user'])
            ->where('vendor_id', $vendorId)
            ->findOrFail($id);

        return view('Vendor.pages.damaged.show', ['item' => $item]);
    }
}
