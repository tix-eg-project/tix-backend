@extends('Admin.layout.app')
@section('title', __('messages.order_details'))

@section('content')
@php
$statusColors = [
'placed' => 'bg-secondary-subtle text-secondary border border-secondary-subtle',
'paid' => 'bg-success-subtle text-success border border-success-subtle',
'shipped' => 'bg-info-subtle text-info border border-info-subtle',
'delivered'=> 'bg-primary-subtle text-primary border border-primary-subtle',
'canceled' => 'bg-danger-subtle text-danger border border-danger-subtle',
'pending' => 'bg-warning-subtle text-warning border border-warning-subtle',
];
$statusClass = $statusColors[$order->status] ?? 'bg-secondary-subtle text-secondary border border-secondary-subtle';

// ملخص مالي معتمد على حقول الأوردر
$subtotal = (float) ($order->subtotal ?? 0);
$shipping = (float) ($order->shipping_price ?? 0);
$couponAmount = (float) ($order->coupon_amount ?? 0);
$finalTotal = (float) ($order->total ?? ($subtotal + $shipping - $couponAmount));
@endphp

<div class="row g-4">

    {{-- بيانات العميل --}}
    <div class="col-xl-6">
        <div class="card rounded-4 custom-card bg-light text-dark border-0 shadow-sm h-100">
            <div class="card-body">
                <h5 class="card-title mb-3">{{ __('messages.customer_info') }}</h5>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item bg-transparent"><strong>{{ __('messages.name') }}:</strong> {{ $order->user?->name ?? '-' }}</li>
                    <li class="list-group-item bg-transparent"><strong>{{ __('messages.phone') }}:</strong> {{ $order->contact_phone ?? '-' }}</li>
                    <li class="list-group-item bg-transparent"><strong>{{ __('messages.address') }}:</strong> {{ $order->contact_address ?? '-' }}</li>
                    <li class="list-group-item bg-transparent"><strong>{{ __('messages.country') }}:</strong> {{ $order->shipping_zone_name ?? '-' }}</li>
                </ul>
            </div>
        </div>
    </div>

    {{-- بيانات الأوردر --}}
    <div class="col-xl-6">
        <div class="card rounded-4 custom-card bg-light text-dark border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <h5 class="card-title mb-0">{{ __('messages.order_info') }}</h5>
                    <span class="badge {{ $statusClass }} px-3 py-2 text-capitalize">{{ $order->status }}</span>
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item bg-transparent"><strong>{{ __('messages.order_id') }}:</strong> {{ $order->id }}</li>
                    <li class="list-group-item bg-transparent"><strong>{{ __('messages.payment_method') }}:</strong> {{ $order->payment_method_name ?? '-' }}</li>
                    <li class="list-group-item bg-transparent"><strong>{{ __('messages.coupon') }}:</strong>
                        {{ $order->coupon_code ? $order->coupon_code . ' (' . number_format($couponAmount,2) . ')' : '-' }}
                    </li>
                    <li class="list-group-item bg-transparent"><strong>{{ __('messages.created_at') }}:</strong> {{ $order->created_at->format('Y-m-d') }}</li>
                    <li class="list-group-item bg-transparent"><strong>{{ __('messages.delivery_date') }}:</strong> {{ optional($order->delivered_at)->format('Y-m-d') ?? '-' }}</li>
                </ul>
            </div>
        </div>
    </div>

</div>

{{-- ملخص مالي --}}
<div class="card rounded-4 custom-card bg-light text-dark border-0 shadow-sm mt-4">
    <div class="card-body">
        <h5 class="card-title mb-3">{{ __('messages.order_summary') }}</h5>
        <div class="row g-3">
            <div class="col-md-3">
                <div class="p-3 rounded-4 border bg-white h-100">
                    <div class="text-muted small">{{ __('messages.subtotal') }}</div>
                    <div class="fs-5 fw-semibold mt-1">{{ number_format($subtotal, 2) }} {{ __('messages.currency') }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-3 rounded-4 border bg-white h-100">
                    <div class="text-muted small">{{ __('messages.shipping') }}</div>
                    <div class="fs-5 fw-semibold mt-1">{{ number_format($shipping, 2) }} {{ __('messages.currency') }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-3 rounded-4 border bg-white h-100">
                    <div class="text-muted small">{{ __('messages.final_total') }}</div>
                    <div class="fs-4 fw-bold mt-1">{{ number_format($finalTotal, 2) }} {{ __('messages.currency') }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- عناصر الطلب --}}
<div class="card rounded-4 custom-card bg-light text-dark border-0 shadow-sm mt-4">
    <div class="card-body">
        <h5 class="card-title">{{ __('messages.order_items') }}</h5>
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>{{ __('messages.image') }}</th>
                        <th>{{ __('messages.product') }}</th>
                        <th>{{ __('messages.unit_price') }}</th>
                        <th>{{ __('messages.quantity') }}</th>
                        <th>{{ __('messages.total') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($order->items as $item)
                    @php
                    $img = $item->product?->image ?? $item->product_image;
                    $unit = (float) $item->price_after;
                    $line = $unit * (int) $item->quantity;
                    @endphp
                    <tr>
                        <td>
                            @if($img)
                            <img src="{{ $img }}" width="60" height="60" class="rounded border bg-white object-fit-cover" alt="">
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>{{ $item->product?->name ?? $item->product_name }}</td>
                        <td>{{ number_format($unit, 2) }} {{ __('messages.currency') }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ number_format($line, 2) }} {{ __('messages.currency') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">{{ __('messages.no_data') }}</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3 d-flex gap-2">
            <a href="{{ route('admin.orders.edit', $order->id) }}" class="btn btn-primary">
                <i class="bi bi-pencil-square me-1"></i>{{ __('messages.edit') }}
            </a>
            <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">{{ __('messages.Back') }}</a>
        </div>
    </div>
</div>
@endsection