@extends('Admin.layout.app')

@section('title', __('messages.returns'))

@section('content')
@php
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

$order = $req->order;
$user  = $req->user;
$vend  = $req->vendor;
$oi    = $req->orderItem;
$prd   = $oi?->product;

/* ================== Image Helpers ================== */
$normalizeImg = function ($src) {
    if (!$src) return null;
    $src = trim((string)$src);

    // URL كامل أو data URI
    if (Str::startsWith($src, ['http://','https://','//','data:image'])) return $src;

    // يبدأ بـ "/" → مسار مطلق على نفس الدومين
    if (Str::startsWith($src, ['/'])) return $src;

    // داخل public مباشرة
    if (file_exists(public_path($src))) return asset($src);

    // على public disk
    if (Storage::disk('public')->exists($src)) return asset('storage/'.$src);

    // على الديسك الافتراضي
    if (Storage::exists($src)) return Storage::url($src);

    // معالجة storage/...
    $trimmed = preg_replace('#^/?storage/#', '', $src);
    if ($trimmed !== $src && Storage::disk('public')->exists($trimmed)) {
        return asset('storage/'.$trimmed);
    }

    // محاولة أخيرة: اعتباره Relative وتحويله لمطلق
    return url($src);
};

/**
 * ترتيب المرشحين: أولوية حقول الأيتم (زي صفحة الأوردرات)، ثم المنتج كـ fallback.
 */
$pickImageFromItemThenProduct = function ($oi, $prd) use ($normalizeImg) {
    $candidates = [
        // من سطر الأوردر (زي شاشة الأوردرات)
        $oi?->image_url      ?? null,
        $oi?->product_image  ?? null,
        $oi?->image          ?? null,
        $oi?->thumbnail      ?? null,

        // ثم من المنتج
        $prd?->image_url     ?? null,
        $prd?->main_image_url?? null,
        $prd?->thumbnail_url ?? null,
        $prd?->image         ?? null,
        $prd?->thumbnail     ?? null,
        $prd?->photo         ?? null,
        $prd?->photo_path    ?? null,
    ];

    foreach ($candidates as $raw) {
        if (!$raw) continue;
        $url = $normalizeImg($raw);
        if ($url) return $url;
    }
    return null;
};

/* ========= بناء الصورة والبيانات الأساسية ========= */
$img  = $pickImageFromItemThenProduct($oi, $prd);
$sku  = $oi?->sku  ?? $prd?->sku  ?? '-';
$name = $oi?->name ?? $prd?->name ?? '-';

// كميات
$qty    = (int) ($oi?->quantity ?? 0);
$retQty = (int) $req->quantity;

// خصائص/أوبشنز
$attrs = $oi->attributes ?? $oi->options ?? $oi->variant_options ?? null;
if (is_string($attrs)) { $x=json_decode($attrs,true); if (json_last_error()===JSON_ERROR_NONE) $attrs=$x; }
if (!is_array($attrs)) $attrs = [];

/* ========= دوال أرقام عامة ========= */
$pick = function($obj, array $keys, $default=null) {
    foreach ($keys as $k) { if (isset($obj->{$k}) && is_numeric($obj->{$k})) return (float)$obj->{$k}; }
    return $default;
};

$orderSubtotal = $order ? $pick($order, ['subtotal','items_subtotal','subtotal_before_discount','sub_total'], null) : null;
$orderDiscount = $order ? $pick($order, ['discount','order_discount','discount_total','coupon_discount'], 0) : 0;
$orderShipping = $order ? $pick($order, [
    'shipping_total','shipping','delivery_fee','shipping_cost','delivery_cost',
    'shipping_fees','shipping_fee','delivery','shippingPrice',
    'shipping_amount','delivery_amount','shipping_price','delivery_price','shipping_charge','delivery_charge'
], 0) : 0;
$orderTotal = $order ? $pick($order, ['total','grand_total','order_total'], null) : null;

if (is_null($orderSubtotal) && $order && is_iterable($order->items ?? [])) {
    $orderSubtotal = 0;
    foreach ($order->items as $it) {
        $unit = null;
        foreach (['unit_price_after','unit_price','final_price','price_after','net_price','price'] as $col) {
            if (isset($it->{$col}) && is_numeric($it->{$col})) { $unit = (float)$it->{$col}; break; }
        }
        if (is_null($unit) && isset($it->total) && $it->quantity > 0) {
            $unit = (float)$it->total / (int)$it->quantity;
        }
        $orderSubtotal += max(0, ($unit ?? 0) * (int)$it->quantity);
    }
}
if (is_null($orderTotal) && !is_null($orderSubtotal)) {
    $orderTotal = max(0, round(($orderSubtotal - $orderDiscount) + $orderShipping, 2));
}

/* ========= أسعار السطر ========= */
$unit = null;
foreach (['unit_price_after','unit_price','final_price','price_after','net_price','price'] as $col) {
    if (isset($oi->{$col}) && is_numeric($oi->{$col})) { $unit = (float)$oi->{$col}; break; }
}
if (is_null($unit) && isset($oi->total) && $qty > 0) { $unit = (float)$oi->total / $qty; }

$lineUnitEffective = $unit;
if (!is_null($orderSubtotal) && $orderSubtotal > 0 && $orderDiscount > 0 && $qty > 0) {
    $lineSubtotal   = ($unit ?? 0) * $qty;
    $share          = $lineSubtotal / $orderSubtotal;
    $lineDiscount   = round($orderDiscount * $share, 2);
    $effectiveLine  = max(0, round($lineSubtotal - $lineDiscount, 2));
    $lineUnitEffective = round($effectiveLine / $qty, 2);
}

$lineTotal = isset($oi->total) && is_numeric($oi->total)
    ? (float)$oi->total
    : (is_numeric($unit) ? $unit * $qty : null);

/* ========= return_address آمن ========= */
$ra = $req->return_address;
if (is_string($ra)) { $x=json_decode($ra,true); $ra = json_last_error()===JSON_ERROR_NONE ? $x : []; }
$ra = is_array($ra) ? $ra : [];
$ra_name    = data_get($ra, 'name');
$ra_phone   = data_get($ra, 'phone');
$ra_city    = data_get($ra, 'city');
$ra_address1= data_get($ra, 'address1') ?? data_get($ra, 'address');
$ra_address2= data_get($ra, 'address2');
$ra_notes   = data_get($ra, 'notes');

/* ========= ترجمات سريعة ========= */
$t = function(string $key, string $fallback) {
    $v = __($key);
    return $v === $key ? $fallback : $v;
};

$currency = $order->currency_code
    ?? $order->currency
    ?? config('app.currency')
    ?? 'EGP';

$orderStatusText = $order?->status_label
    ?? ($order?->delivered_at ? __('messages.delivered') : __('messages.order_status_processing'));

$pmRaw = $order->payment_method
    ?? $order->payment_method_name
    ?? $order->payment_method_title
    ?? $order->payment
    ?? $order->pay_method
    ?? $order->payment_type
    ?? null;

$pmKey = is_string($pmRaw) ? strtolower(trim($pmRaw)) : null;
$pmLabel = match (true) {
    $pmKey && str_contains($pmKey, 'wallet') => __('messages.wallet'),
    $pmKey && (str_contains($pmKey, 'card') || str_contains($pmKey, 'visa') || str_contains($pmKey, 'master') || str_contains($pmKey, 'stripe')) => __('messages.card'),
    default => $pmRaw ?: '-',
};

$couponCode = null;
foreach (['coupon_code','coupon','voucher_code','promo_code'] as $ck) {
    if (!empty($order?->{$ck})) { $couponCode = (string)$order->{$ck}; break; }
}
@endphp

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">
        {{ __('messages.returns') }} #{{ $req->id }}
        <span class="badge bg-info ms-2">{{ $req->status_label }}</span>
    </h4>

    <div>
        <a href="{{ route('admin.returns.edit', $req->id) }}" class="btn btn-primary">
            <i class="bx bx-edit me-1"></i> {{ __('messages.edit') }}
        </a>
        <a href="{{ route('admin.returns.index') }}" class="btn btn-outline-secondary">
            {{ __('messages.back') }}
        </a>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('messages.close') }}"></button>
</div>
@endif

<div class="row g-3">
    {{-- ======= Order Head Info & Summary (match Orders page) ======= --}}
    <div class="row g-3">
        {{-- Order Info --}}
        <div class="col-12 col-xl-6">
            <div class="card rounded-4 border-0 shadow-sm h-100">
                <div class="card-body">
                    <h5 class="mb-3">{{ __('messages.order_info') }}</h5>
                    <dl class="row mb-0">
                        <dt class="col-5">{{ __('messages.status') }}</dt>
                        <dd class="col-7">{{ $orderStatusText }}</dd>

                        <dt class="col-5">{{ __('messages.order_id') }}</dt>
                        <dd class="col-7">#{{ $order?->id ?? '-' }}</dd>

                        <dt class="col-5">{{ __('messages.payment_method') }}</dt>
                        <dd class="col-7">{{ $pmLabel ?: '-' }}</dd>

                        <dt class="col-5">{{ __('messages.coupon') }}</dt>
                        <dd class="col-7">{{ $couponCode ?: '-' }}</dd>

                        <dt class="col-5">{{ __('messages.created_at') }}</dt>
                        <dd class="col-7">{{ optional($order?->created_at)->format('Y-m-d') ?? '-' }}</dd>

                        <dt class="col-5">{{ __('messages.delivered_at') }}</dt>
                        <dd class="col-7">{{ optional($order?->delivered_at)->format('Y-m-d') ?? '-' }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        {{-- Order Summary --}}
        <div class="col-12 col-xl-6">
            <div class="card rounded-4 border-0 shadow-sm h-100">
                <div class="card-body">
                    <h5 class="mb-3">{{ __('messages.order_summary') }}</h5>
                    <dl class="row mb-0">
                        <dt class="col-6">{{ __('messages.subtotal') }}</dt>
                        <dd class="col-6 text-end">
                            {{ is_numeric($orderSubtotal) ? number_format($orderSubtotal,2).' '.$currency : '-' }}
                        </dd>

                        <dt class="col-6">{{ __('messages.shipping') }}</dt>
                        <dd class="col-6 text-end">
                            {{ number_format($orderShipping ?? 0, 2).' '.$currency }}
                        </dd>

                        <dt class="col-6 fw-bold">{{ __('messages.final_total') }}</dt>
                        <dd class="col-6 text-end fw-bold">
                            {{ is_numeric($orderTotal) ? number_format($orderTotal,2).' '.$currency : '-' }}
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    {{-- العميل --}}
    <div class="col-12 col-xl-4">
        <div class="card rounded-4 border-0 shadow-sm h-100">
            <div class="card-body">
                <h5 class="mb-3">{{ __('messages.customer') }}</h5>
                <dl class="row mb-0">
                    <dt class="col-5">{{ __('messages.user') }}</dt>
                    <dd class="col-7">{{ $user?->name ?? '-' }}</dd>
                    <dt class="col-5">{{ __('messages.email') }}</dt>
                    <dd class="col-7">{{ $user?->email ?? '-' }}</dd>
                    <dt class="col-5">{{ __('messages.phone') }}</dt>
                    <dd class="col-7">{{ $user?->phone ?? '-' }}</dd>
                </dl>
            </div>
        </div>
    </div>

    {{-- البائع --}}
    <div class="col-12 col-xl-4">
        <div class="card rounded-4 border-0 shadow-sm h-100">
            <div class="card-body">
                <h5 class="mb-3">{{ __('messages.vendor') }}</h5>
                <dl class="row mb-0">
                    <dt class="col-5">{{ __('messages.vendor') }}</dt>
                    <dd class="col-7">{{ $vend?->name ?? '-' }}</dd>
                    <dt class="col-5">{{ __('messages.email') }}</dt>
                    <dd class="col-7">{{ $vend?->email ?? '-' }}</dd>
                    <dt class="col-5">{{ __('messages.phone') }}</dt>
                    <dd class="col-7">{{ $vend?->phone ?? '-' }}</dd>
                </dl>
            </div>
        </div>
    </div>
</div>

{{-- تفاصيل الاسترجاع --}}
<div class="card rounded-4 border-0 shadow-sm mt-3">
    <div class="card-body">
        <h5 class="mb-3">{{ __('messages.return_details') }}</h5>
        <div class="row g-3">
            <div class="col-12 col-md-6">
                <dl class="row mb-0">
                    <dt class="col-5">{{ __('messages.status') }}</dt>
                    <dd class="col-7">{{ $req->status_label }}</dd>
                    <dt class="col-5">{{ __('messages.reason') }}</dt>
                    <dd class="col-7">{{ $req->reason_label ?? '-' }}</dd>
                    @if($req->reason_text)
                        <dt class="col-5">{{ __('messages.reason_text') }}</dt>
                        <dd class="col-7">{{ $req->reason_text }}</dd>
                    @endif
                    <dt class="col-5">{{ __('messages.requested_return_qty') }}</dt>
                    <dd class="col-7">{{ $retQty }}</dd>

                    <dt class="col-5">{{ __('messages.approved_at') }}</dt>
                    <dd class="col-7">{{ optional($req->approved_at)->format('Y-m-d H:i') ?? '-' }}</dd>
                    <dt class="col-5">{{ __('messages.received_at') }}</dt>
                    <dd class="col-7">{{ optional($req->received_at)->format('Y-m-d H:i') ?? '-' }}</dd>
                    <dt class="col-5">{{ __('messages.refunded_at') }}</dt>
                    <dd class="col-7">{{ optional($req->refunded_at)->format('Y-m-d H:i') ?? '-' }}</dd>
                </dl>
            </div>

            <div class="col-12 col-md-6">
                <dl class="row mb-0">
                    @if($req->payout_wallet_phone)
                        <dt class="col-5">{{ __('messages.wallet_phone') }}</dt>
                        <dd class="col-7">{{ $req->payout_wallet_phone }}</dd>
                    @endif
                </dl>

                <hr class="my-3">

                @php
                    $addr = $req->return_address;

                    if (is_string($addr)) {
                        $decoded = json_decode($addr, true);
                        $addr = is_array($decoded) ? $decoded : ['city' => trim($addr)];
                    } elseif (!is_array($addr)) {
                        $addr = [];
                    }

                    $ra_name    = trim((string)($addr['name']     ?? ''));
                    $ra_phone   = trim((string)($addr['phone']    ?? ''));
                    $ra_city    = trim((string)($addr['city']     ?? ''));
                    $ra_address1= trim((string)($addr['address1'] ?? ($addr['address'] ?? '')));
                    $ra_address2= trim((string)($addr['address2'] ?? ''));
                    $ra_notes   = trim((string)($addr['notes']    ?? ''));

                    $lines = [];
                    if ($ra_city    !== '') { $lines[] = $ra_city; }
                    if ($ra_address1!== '' || $ra_address2 !== '') {
                        $lines[] = trim($ra_address1 . ($ra_address2 ? ', '.$ra_address2 : ''));
                    }
                    if ($ra_notes   !== '') { $lines[] = $ra_notes; }
                @endphp

                <h6 class="mb-2">{{ __('messages.return_address') }}</h6>
                @if(count($lines))
                    <address class="mb-0 small text-muted">
                        @foreach($lines as $L)
                            {{ $L }}<br>
                        @endforeach

                        @if($ra_name !== '' || $ra_phone !== '')
                            <span class="d-block mt-1">{{ $ra_name }}@if($ra_phone) — {{ $ra_phone }}@endif</span>
                        @endif
                    </address>
                @else
                    <p class="text-muted mb-0">-</p>
                @endif
            </div>
        </div>

        {{-- ملخص مبالغ المسترجع (بعد القرار) --}}
        <div class="row g-3 mt-3">
            <div class="col-6 col-md-3">
                <div class="p-3 bg-light rounded-3 h-100">
                    <div class="small text-muted">{{ __('messages.refund_subtotal') }}</div>
                    <div class="fs-6 fw-semibold">{{ number_format($req->refund_subtotal ?? 0, 2) }}</div>
                </div>
            </div>
            {{-- <div class="col-6 col-md-3">
                <div class="p-3 bg-light rounded-3 h-100">
                    <div class="small text-muted">{{ __('messages.refund_fee') }}</div>
                    <div class="fs-6 fw-semibold">{{ number_format($req->refund_fee ?? 0, 2) }}</div>
                </div>
            </div> --}}
            <div class="col-6 col-md-3">
                <div class="p-3 bg-light rounded-3 h-100">
                    <div class="small text-muted">{{ __('messages.refund_shipping') }}</div>
                    <div class="fs-6 fw-semibold">{{ number_format($req->refund_shipping ?? 0, 2) }}</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="p-3 bg-light rounded-3 h-100">
                    <div class="small text-muted">{{ __('messages.refund_total') }}</div>
                    <div class="fs-5 fw-bold">{{ number_format($req->refund_total ?? 0, 2) }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- سطر المنتج كأنه في عرض الأوردر --}}
<div class="card rounded-4 border-0 shadow-sm mt-3">
    <div class="card-body">
        <h5 class="mb-3">{{ __('messages.order_item') }}</h5>

        <div class="d-flex align-items-start gap-3">
            <div style="width:80px;height:80px;" class="rounded overflow-hidden bg-light border">
                @if($img)
                    <img
                        src="{{ $img }}"
                        alt="{{ $name }}"
                        class="w-100 h-100 object-fit-cover"
                        loading="lazy"
                        referrerpolicy="no-referrer"
                        onerror="this.style.display='none'; this.nextElementSibling?.classList.remove('d-none');"
                    >
                    <div class="w-100 h-100 d-flex align-items-center justify-content-center text-muted d-none">IMG</div>
                @else
                    <div class="w-100 h-100 d-flex align-items-center justify-content-center text-muted">IMG</div>
                @endif
            </div>

            <div class="flex-grow-1">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="fw-semibold">{{ $name }}</div>
                        <div class="small text-muted">SKU: {{ $sku }}</div>
                        @if(!empty($attrs))
                            <div class="small">
                                @foreach($attrs as $k => $v)
                                    <span class="badge bg-light text-dark border me-1 mb-1">
                                        {{ $k }}: {{ is_array($v) ? implode(', ', $v) : $v }}
                                    </span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    <div class="text-end">
                        @if(is_numeric($unit))
                            <div class="small text-muted">{{ __('messages.unit_price') }} ({{ __('messages.before_discount') }}): {{ number_format($unit,2) }}</div>
                        @endif
                        @if(is_numeric($lineUnitEffective))
                            <div class="small text-muted">{{ __('messages.unit_price_effective') }}: {{ number_format($lineUnitEffective,2) }}</div>
                        @endif
                        @if(is_numeric($lineTotal))
                            <div class="small text-muted">{{ __('messages.line_total') }}: {{ number_format($lineTotal,2) }}</div>
                        @endif
                    </div>
                </div>

                <div class="mt-2">
                    <span class="badge bg-secondary">{{ __('messages.ordered_qty') }}: {{ $qty }}</span>
                    <span class="badge bg-info ms-1">{{ __('messages.requested_return_qty') }}: {{ $retQty }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection