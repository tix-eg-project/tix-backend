@extends('Admin.layout.app')

@section('title', __('messages.Show Sub Product'))
@section('page_title', __('messages.Show Sub Product'))

@section('content')
<div class="container-xxl container-p-y">

    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
        <h4 class="mb-0">{{ __('messages.Show Sub Product') }}</h4>
        <div class="d-flex gap-2">
            <a href="{{ route('products-variant.index', $product) }}" class="btn btn-outline-secondary">
                <i class="bx bx-left-arrow-alt"></i> {{ __('messages.Back') }}
            </a>
            <a href="{{ route('products-variant.edit', [$product, $item]) }}" class="btn btn-primary">
                <i class="fa-regular fa-pen-to-square"></i> {{ __('messages.Edit') }}
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            @php
            $pairs = collect($item->selections ?? []);
            $variantIds = $pairs->map(fn($r)=> $r['product_variant_id'] ?? $r['variant_id'] ?? null)->filter()->unique();
            $valueIds = $pairs->map(fn($r)=> $r['product_variant_value_id'] ?? $r['value_id'] ?? null)->filter()->unique();

            $variants = \App\Models\ProductVariant::whereIn('id', $variantIds)->get()->keyBy('id');
            $values = \App\Models\ProductVariantValue::whereIn('id', $valueIds)->get()->keyBy('id');
            $loc = app()->getLocale();
            @endphp

            <div class="mb-3">
                <strong>{{ __('messages.Selections') }}:</strong>
                <div class="mt-2">
                    @forelse($pairs as $p)
                    @php
                    $variantId = $p['product_variant_id'] ?? $p['variant_id'] ?? null;
                    $valueId = $p['product_variant_value_id'] ?? $p['value_id'] ?? null;

                    $v = $variantId ? ($variants[$variantId] ?? null) : null;
                    $val = $valueId ? ($values[$valueId] ?? null) : null;

                    if ($v && method_exists($v, 'getTranslation')) $vName = $v->getTranslation('name', $loc, true);
                    else {
                    $raw = $v->name ?? null;
                    $vName = is_array($raw) ? ($raw[$loc] ?? (reset($raw) ?: '')) : (string)($raw ?? '');
                    }
                    $vName = $vName ?: __('messages.Variant');

                    if ($val && method_exists($val, 'getTranslation')) $valName = $val->getTranslation('name', $loc, true);
                    else {
                    $raw = $val->name ?? null;
                    $valName = is_array($raw) ? ($raw[$loc] ?? (reset($raw) ?: '')) : (string)($raw ?? '');
                    }
                    $valName = $valName ?: __('messages.Value');
                    @endphp

                    <span class="badge bg-light text-dark border me-1 mb-1">{{ $vName }}: {{ $valName }}</span>
                    @empty
                    <span class="text-muted">{{ __('(No selections)') }}</span>
                    @endforelse
                </div>
            </div>

            <div class="mb-3">
                <strong>{{ __('messages.Price') }}:</strong>
                <span>{{ number_format((float) $item->price, 2) }}</span>
            </div>

            <div class="mb-3">
                <strong>{{ __('messages.Quantity') }}:</strong>
                <span>{{ $item->quantity }}</span>
            </div>
        </div>
    </div>
</div>
@endsection