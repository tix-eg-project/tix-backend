<?php

namespace App\Http\Controllers\Web\Admin\Inventory;

use App\Http\Controllers\Controller;
use App\Models\DamagedStock;
use Illuminate\Http\Request;

class AdminDamagedStockController extends Controller
{
    public function index(Request $request)
    {
        $search   = trim((string)$request->input('search', ''));
        $vendorId = $request->input('vendor_id');
        $reason   = $request->input('reason_code');

        // app/Http/Controllers/Web/Admin/Inventory/AdminDamagedStockController.php

        $items = DamagedStock::query()
            ->with([
                'product',
                'vendor:id,name',
                'variantItem',
                'returnRequest.user:id,name',
            ])
            ->when($search !== '', function ($q) use ($search) {
                $q->whereHas('product', fn($pq) => $pq->where('name', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%"))
                    ->orWhere('id', (int)$search);
            })
            ->when($vendorId, fn($q) => $q->where('vendor_id', (int)$vendorId))
            ->when($reason,   fn($q) => $q->where('reason_code', (int)$reason))
            ->latest('id')
            ->paginate(20);


        return view('Admin.pages.damaged.index', compact('items'));
    }

    public function show(DamagedStock $damaged_stock)
    {
        $damaged_stock->loadMissing(['product', 'vendor', 'returnRequest.order', 'returnRequest.user']);
        return view('Admin.pages.damaged.show', ['item' => $damaged_stock]);
    }
}
