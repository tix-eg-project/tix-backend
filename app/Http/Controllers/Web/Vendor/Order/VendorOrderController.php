<?php

namespace App\Http\Controllers\Web\Vendor\Order;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Enums\OrderStatusEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class VendorOrderController extends Controller
{
    public function index(Request $request)
    {
        $vendorId = auth('vendor')->id();

        $pmColumn = Schema::hasColumn('orders', 'payment_method_name')
            ? 'payment_method_name'
            : (Schema::hasColumn('orders', 'payment_method') ? 'payment_method' : null);

        $query = Order::query()
            ->forVendor($vendorId)
            ->with(['user', 'items.product']);

        if ($request->filled('search')) {
            $search = trim($request->string('search'));
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                    ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($pmColumn && $request->filled('payment_method')) {
            $query->where($pmColumn, $request->string('payment_method'));
        }

        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->date('from'));
        }
        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->date('to'));
        }

        $orders = $query->latest()->paginate(10)->appends($request->query());

        $orders->getCollection()->transform(function ($order) use ($vendorId) {
            $vendorItems = $order->items->filter(function ($i) use ($vendorId) {
                return (int) $i->vendor_id === (int) $vendorId
                    || ((int) optional($i->product)->vendor_id === (int) $vendorId);
            })->values();

            $order->setRelation('items', $vendorItems);
            $order->vendor_total = $vendorItems->sum(fn($i) => (float) $i->price_after * (int) $i->quantity);

            return $order;
        });

        $statusOptions = Order::forVendor($vendorId)
            ->select('status')->distinct()->pluck('status')->filter()->values();

        $paymentOptions = collect();
        if ($pmColumn) {
            $paymentOptions = Order::forVendor($vendorId)
                ->select($pmColumn . ' as pm')
                ->whereNotNull($pmColumn)
                ->distinct()
                ->pluck('pm')
                ->filter()
                ->values();
        }

        return view('Vendor.pages.orders.index', compact('orders', 'statusOptions', 'paymentOptions'));
    }



    public function show(Order $order)
    {
        $vendorId = auth('vendor')->id();

        abort_unless(
            $order->items()
                ->where('vendor_id', $vendorId)
                ->orWhereHas('product', fn($p) => $p->where('vendor_id', $vendorId))
                ->exists(),
            403
        );

        $order->load(['user', 'items.product']);

        $vendorItems = $order->items->filter(function ($i) use ($vendorId) {
            return (int)($i->vendor_id) === (int)$vendorId
                || (optional($i->product)->vendor_id && (int)$i->product->vendor_id === (int)$vendorId);
        })->values();

        $order->setRelation('items', $vendorItems);
        $vendorTotal = $vendorItems->sum(fn($i) => (float)$i->price_after * (int)$i->quantity);

        return view('Vendor.pages.orders.show', compact('order', 'vendorTotal'));
    }

    public function edit(Order $order)
    {
        $vendorId = auth('vendor')->id();

        abort_unless(
            $order->items()
                ->where('vendor_id', $vendorId)
                ->orWhereHas('product', fn($p) => $p->where('vendor_id', $vendorId))
                ->exists(),
            403
        );

        $statuses = OrderStatusEnum::cases();
        return view('Vendor.pages.orders.edit', compact('order', 'statuses'));
    }

    public function update(Request $request, Order $order)
    {
        $vendorId = auth('vendor')->id();

        abort_unless(
            $order->items()
                ->where('vendor_id', $vendorId)
                ->orWhereHas('product', fn($p) => $p->where('vendor_id', $vendorId))
                ->exists(),
            403
        );

        $request->validate([
            'status'        => 'required|in:' . implode(',', array_column(OrderStatusEnum::cases(), 'value')),
            'delivery_date' => 'nullable|date',
        ]);

        $deliveredAt = $request->delivery_date;
        if ($request->status === 'delivered' && empty($deliveredAt)) {
            $deliveredAt = now();
        }

        $order->update([
            'status'       => $request->status,
            'delivered_at' => $deliveredAt,
        ]);

        return redirect()->route('vendor.orders.index')
            ->with('success', __('messages.updated_successfully'));
    }

    public function destroy(Order $order)
    {
        $order->delete();
        return back()->with('success', __('messages.deleted_successfully'));
    }
}
