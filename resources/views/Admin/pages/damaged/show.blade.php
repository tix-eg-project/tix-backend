@extends('Admin.layout.app')

@section('title', __('messages.damaged_stocks'))

@php
    use Illuminate\Support\Str;
    use Illuminate\Support\Facades\Storage;

    $normalizeImg = function ($src) {
        if (!$src) return null;
        $src = trim((string)$src);

        if (Str::startsWith($src, ['http://','https://','//','data:image'])) return $src;
        if (Str::startsWith($src, ['/'])) return $src;

        if (file_exists(public_path($src))) return asset($src);
        if (Storage::disk('public')->exists($src)) return asset('storage/'.$src);
        if (Storage::exists($src)) return Storage::url($src);

        $trimmed = preg_replace('#^/?storage/#', '', $src);
        if ($trimmed !== $src && Storage::disk('public')->exists($trimmed)) {
            return asset('storage/'.$trimmed);
        }

        return url($src);
    };

    $pickImageForDamaged = function ($dam) use ($normalizeImg) {
        $p   = $dam?->product;
        $rr  = $dam?->returnRequest;
        $oi  = $rr?->orderItem;

        $candidates = [
            // من الأيتم المرتبط عبر طلب الاسترجاع
            $oi?->image_url ?? null,
            $oi?->product_image ?? null,
            $oi?->image ?? null,
            $oi?->thumbnail ?? null,

            // من الڤاريانت إن وُجد
            $dam?->variantItem?->image_url ?? null,
            $dam?->variantItem?->image ?? null,

            // من المنتج
            $p?->image_url ?? null,
            $p?->main_image_url ?? null,
            $p?->thumbnail_url ?? null,
            $p?->image ?? null,
            $p?->thumbnail ?? null,
            $p?->photo ?? null,
            $p?->photo_path ?? null,
        ];

        foreach ($candidates as $raw) {
            if (!$raw) continue;
            $url = $normalizeImg($raw);
            if ($url) return $url;
        }
        return null;
    };
@endphp

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">{{ __('messages.damaged_stocks') }} #{{ $item->id }}</h4>
    <div>
        <a href="{{ route('admin.damaged-stocks.index') }}" class="btn btn-outline-secondary">{{ __('messages.back') }}</a>
    </div>
</div>

<div class="row g-3">
    {{-- معلومات المنتج --}}
    <div class="col-12 col-lg-6">
        <div class="card rounded-4 border-0 shadow-sm">
            <div class="card-body">
                <h5 class="mb-3">{{ __('messages.product') }}</h5>
                @php
                    $p   = $item->product;
                    $img = $pickImageForDamaged($item) ?? asset('assets/img/placeholder.png');

                    $variantText = $item->variantItem?->name
                        ?? $item->variantItem?->options_text
                        ?? $item->variantItem?->sku
                        ?? null;

                    $reasonLabel = $item->reason_code
                        ? \App\Enums\ReturnReasonEnum::tryFrom((int)$item->reason_code)?->label()
                        : null;
                @endphp

                <div class="d-flex align-items-center gap-3 mb-3">
                    <div style="width:72px;height:72px;" class="rounded overflow-hidden bg-light border">
                        <img src="{{ $img }}"
                             alt=""
                             class="w-100 h-100"
                             style="object-fit:cover"
                             loading="lazy"
                             onerror="this.src='{{ asset('assets/img/placeholder.png') }}'">
                    </div>
                    <div class="min-w-0">
                        <div class="fw-semibold fs-6 text-truncate">{{ $p?->name ?? '-' }}</div>
                        <div class="text-muted small">
                            @if($p?->sku)<span>SKU: {{ $p->sku }}</span>@endif
                            @if($variantText)
                                <span class="ms-1">• {{ $variantText }}</span>
                            @endif
                        </div>
                        <div class="text-muted small">
                            {{ __('messages.vendor') }}: {{ $item->vendor?->name ?? '-' }}
                        </div>
                    </div>
                </div>

                <hr>

                <dl class="row mb-0">
                    <dt class="col-sm-4">{{ __('messages.quantity') }}</dt>
                    <dd class="col-sm-8">{{ $item->quantity }}</dd>

                    <dt class="col-sm-4">{{ __('messages.reason') }}</dt>
                    <dd class="col-sm-8">
                        {{ $reasonLabel ?? '-' }}
                        @if($item->reason_text)
                            <div class="text-muted small mt-1">{{ $item->reason_text }}</div>
                        @endif
                    </dd>

                    <dt class="col-sm-4">{{ __('messages.created_at') }}</dt>
                    <dd class="col-sm-8">{{ optional($item->created_at)->format('Y-m-d H:i') }}</dd>
                </dl>
            </div>
        </div>
    </div>

    {{-- روابط ذات صلة --}}
    <div class="col-12 col-lg-6">
        <div class="card rounded-4 border-0 shadow-sm">
            <div class="card-body">
                <h5 class="mb-3">{{ __('messages.related') }}</h5>
                <dl class="row mb-0">
                    <dt class="col-sm-4">{{ __('messages.return_request') }}</dt>
                    <dd class="col-sm-8">
                        @if($item->returnRequest)
                            <a href="{{ route('admin.returns.show', $item->returnRequest->id) }}">
                                #{{ $item->returnRequest->id }}
                            </a>
                        @else
                            -
                        @endif
                    </dd>

                    <dt class="col-sm-4">{{ __('messages.order') }}</dt>
                    <dd class="col-sm-8">
                        @if($item->returnRequest?->order_id)
                            #{{ $item->returnRequest->order_id }}
                        @else
                            -
                        @endif
                    </dd>

                    <dt class="col-sm-4">{{ __('messages.user') }}</dt>
                    <dd class="col-sm-8">{{ $item->returnRequest?->user?->name ?? '-' }}</dd>
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection
