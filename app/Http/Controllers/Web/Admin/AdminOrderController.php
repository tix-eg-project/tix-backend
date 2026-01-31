<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Enums\OrderStatusEnum;
use Illuminate\Http\Request;

class AdminOrderController extends Controller
{

    public function index(Request $request)
    {
        $search        = trim((string) $request->input('search', ''));
        $status        = $request->input('status');             // placed | paid | ...
        $paymentMethod = $request->input('payment_method');     // مثلاً: "cod", "visa", أو الاسم المخزَّن
        $createdFrom   = $request->input('created_from');       // Y-m-d
        $createdTo     = $request->input('created_to');         // Y-m-d
        $delivFrom     = $request->input('delivered_from');     // Y-m-d
        $delivTo       = $request->input('delivered_to');       // Y-m-d

        // خيارات الفلاتر من الداتابيس (Distinct)
        $statusOptions = Order::query()->select('status')->distinct()->pluck('status')->filter()->values();
        $paymentOptions = Order::query()->select('payment_method_name')->distinct()->pluck('payment_method_name')->filter()->values();

        $orders = Order::query()
            ->with(['user:id,name'])
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($qq) use ($search) {
                    if (ctype_digit($search)) {
                        $qq->orWhere('id', (int)$search);
                    }
                    $qq->orWhereHas('user', fn($uq) => $uq->where('name', 'like', "%{$search}%"))
                        ->orWhere('contact_phone', 'like', "%{$search}%")
                        ->orWhere('contact_address', 'like', "%{$search}%")
                        ->orWhere('coupon_code', 'like', "%{$search}%");
                });
            })
            ->when($status, fn($q) => $q->where('status', $status))
            ->when($paymentMethod, fn($q) => $q->where('payment_method_name', $paymentMethod))
            ->when($createdFrom, fn($q) => $q->whereDate('created_at', '>=', $createdFrom))
            ->when($createdTo, fn($q) => $q->whereDate('created_at', '<=', $createdTo))
            ->when($delivFrom, fn($q) => $q->whereDate('delivered_at', '>=', $delivFrom))
            ->when($delivTo, fn($q) => $q->whereDate('delivered_at', '<=', $delivTo))
            ->latest('id')
            ->paginate(20)
            ->appends($request->query());

        return view('Admin.pages.orders.index', compact('orders', 'statusOptions', 'paymentOptions'));
    }

    public function edit(Order $order)
    {
        $statuses = OrderStatusEnum::cases();
        return view('Admin.pages.orders.edit', compact('order', 'statuses'));
    }

    public function update(Request $request, Order $order)
    {
        $request->validate([
            'status'        => 'required|in:' . implode(',', array_column(OrderStatusEnum::cases(), 'value')),
            'delivery_date' => 'nullable|date',
        ]);

        $order->update([
            'status'       => $request->status,
            'delivered_at' => $request->delivery_date,
        ]);

        return redirect()->route('admin.orders.index')
            ->with('success', __('messages.updated_successfully'));
    }

    public function show(Order $order)
    {
        $order->load(['user', 'items.product']);
        return view('Admin.pages.orders.show', compact('order'));
    }

    public function destroy(Order $order)
    {
        $order->delete();
        return back()->with('success', __('messages.deleted_successfully'));
    }
}
