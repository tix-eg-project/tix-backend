{{-- resources/views/Vendor/pages/damaged/index.blade.php --}}
@extends('Vendor.layout.app')

@section('title', __('messages.damaged_stocks'))

@php
    use Illuminate\Support\Str;
    use Illuminate\Support\Facades\Storage;

    // تطبيع أي قيمة لصورة إلى URL صالح
    $normalizeImg = function ($src) {
        if (!$src) return null;
        $src = trim((string)$src);

        if (Str::startsWith($src, ['http://','https://','//','data:image'])) return $src; // URL كامل
        if (Str::startsWith($src, ['/'])) return $src; // مسار مطلق على نفس الدومين

        if (file_exists(public_path($src))) return asset($src);                 // داخل public/
        if (Storage::disk('public')->exists($src)) return asset('storage/'.$src); // storage public
        if (Storage::exists($src)) return Storage::url($src);                     // ديسك افتراضي

        // معالجة storage/...
        $trimmed = preg_replace('#^/?storage/#', '', $src);
        if ($trimmed !== $src && Storage::disk('public')->exists($trimmed)) {
            return asset('storage/'.$trimmed);
        }

        return url($src); // محاولة أخيرة
    };

    // مرشّح صورة لصف التالف: من سطر الأوردر (عبر طلب الاسترجاع) ثم الفاريانت ثم المنتج
    $pickImageForDamaged = function ($dam) use ($normalizeImg) {
        $p  = $dam?->product;
        $rr = $dam?->returnRequest;
        $oi = $rr?->orderItem;

        $candidates = [
            // من الأيتم (نفس منطق الأوردر/المسترجعات)
            $oi?->image_url ?? null,
            $oi?->product_image ?? null,
            $oi?->image ?? null,
            $oi?->thumbnail ?? null,

            // من الڤاريانت (لو عندك صور على الڤاريانت)
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
    <h4 class="mb-0">{{ __('messages.damaged_stocks') }}</h4>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('messages.close') }}"></button>
</div>
@endif

<div class="card rounded-4 border-0 shadow-sm mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('vendor.damaged-stocks.index') }}">
            <div class="row g-3 align-items-end">
                <div class="col-12 col-md-4">
                    <label class="form-label">{{ __('messages.search') }}</label>
                    <input type="text" name="search" class="form-control"
                        value="{{ request('search') }}"
                        placeholder="{{ __('messages.product') }} / SKU / #ID">
                </div>

                <div class="col-12 col-md-2">
                    <button class="btn btn-secondary w-100">
                        <i class="bx bx-filter-alt"></i> {{ __('messages.filter') }}
                    </button>
                </div>

                @if(collect(request()->only(['search','vendor_id','reason_code']))->filter()->isNotEmpty())
                <div class="col-12 col-md-2">
                    <a href="{{ route('vendor.damaged-stocks.index') }}" class="btn btn-outline-secondary w-100">
                        {{ __('messages.clear') }}
                    </a>
                </div>
                @endif
            </div>
        </form>
    </div>
</div>

<div class="card rounded-4 border-0 shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ __('messages.product') }}</th>
                        <th>{{ __('messages.vendor') }}</th>
                        <th>{{ __('messages.quantity') }}</th>
                        <th>{{ __('messages.reason') }}</th>
                        <th>{{ __('messages.created_at') }}</th>
                        <th>{{ __('messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $it)
                        @php
                            $p = $it->product;
                            $img = $pickImageForDamaged($it) ?? asset('assets/img/placeholder.png');

                            $variantText = $it->variantItem?->name
                                ?? $it->variantItem?->options_text
                                ?? $it->variantItem?->sku
                                ?? null;

                            $reasonLabel = $it->reason_code
                                ? \App\Enums\ReturnReasonEnum::tryFrom((int)$it->reason_code)?->label()
                                : null;
                        @endphp
                        <tr>
                            <td>{{ $it->id }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div style="width:48px;height:48px;" class="rounded overflow-hidden bg-light border">
                                        <img src="{{ $img }}" alt=""
                                             class="w-100 h-100" style="object-fit:cover" loading="lazy"
                                             onerror="this.src='{{ asset('assets/img/placeholder.png') }}'">
                                    </div>
                                    <div class="min-w-0">
                                        <div class="fw-semibold text-truncate">{{ $p?->name ?? '-' }}</div>
                                        <div class="text-muted small">
                                            @if($p?->sku)<span>SKU: {{ $p->sku }}</span>@endif
                        @if($variantText)<span class="ms-1">• {{ $variantText }}</span>@endif
                                        </div>
                                        @if($it->returnRequest)
                                        <div class="text-muted small">
                                            {{ __('messages.order') }} #{{ $it->returnRequest->order_id }}
                                            — {{ __('messages.user') }}: {{ $it->returnRequest->user?->name ?? '-' }}
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>{{ $it->vendor?->name ?? '-' }}</td>
                            <td>{{ $it->quantity }}</td>
                            <td>{{ $reasonLabel ?? '-' }}</td>
                            <td>{{ optional($it->created_at)->format('Y-m-d H:i') }}</td>
                            <td>
                                <a href="{{ route('vendor.damaged-stocks.show', $it->id) }}" class="btn btn-sm btn-info" title="{{ __('messages.details') }}">
                                    <i class="fa-regular fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">{{ __('messages.no_data') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center mt-3">
            {{ $items->appends(request()->query())->links('pagination::bootstrap-4') }}
        </div>
    </div>
</div>
@endsection
