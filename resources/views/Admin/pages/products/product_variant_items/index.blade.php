@extends('Admin.layout.app')

@section('title', __('messages.Sub Products'))
@section('page_title', __('messages.Sub Products'))

@section('content')
<div class="container-xxl container-p-y">

    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
        <h4 class="mb-0">{{ __('messages.Sub Products') }}</h4>
        <div class="d-flex gap-2">
            <a href="{{ route('products.edit', $product->id) }}" class="btn btn-outline-secondary">
                <i class="bx bx-left-arrow-alt"></i> {{ __('messages.Back') }}
            </a>
            <a href="{{ route('products-variant.create', $product) }}" class="btn btn-primary">
                <i class="bx bx-plus"></i> {{ __('messages.Add Sub Product') }}
            </a>
        </div>
    </div>

    @if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="table-responsive text-nowrap">
            <table class="table table-hover table-striped text-center align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width:56px">#</th>
                        <th>{{ __('messages.Selections') }}</th>
                        <th>{{ __('messages.Price') }}</th>
                        <th>{{ __('messages.Quantity') }}</th>
                        <th>{{ __('messages.Created At') }}</th>
                        <th class="text-end" style="width:160px">{{ __('messages.Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse(($items ?? $product->variantItems()->latest('id')->get()) as $item)
                    @php
                    $pairs = collect($item->selections ?? []);

                    // IDs (تجميع دفعة واحدة)
                    $variantIds = $pairs->map(fn($r)=> $r['product_variant_id'] ?? $r['variant_id'] ?? null)->filter()->unique();
                    $valueIds = $pairs->map(fn($r)=> $r['product_variant_value_id'] ?? $r['value_id'] ?? null)->filter()->unique();

                    $variants = \App\Models\ProductVariant::whereIn('id', $variantIds)->get()->keyBy('id');
                    $values = \App\Models\ProductVariantValue::whereIn('id', $valueIds)->get()->keyBy('id');
                    $loc = app()->getLocale();

                    $labels = $pairs->map(function($p) use ($variants, $values, $loc) {
                    $variantId = $p['product_variant_id'] ?? $p['variant_id'] ?? null;
                    $valueId = $p['product_variant_value_id'] ?? $p['value_id'] ?? null;

                    $v = $variantId ? ($variants[$variantId] ?? null) : null;
                    $val = $valueId ? ($values[$valueId] ?? null) : null;

                    // variant label
                    if ($v && method_exists($v, 'getTranslation')) $vName = $v->getTranslation('name', $loc, true);
                    else {
                    $raw = $v->name ?? null;
                    $vName = is_array($raw) ? ($raw[$loc] ?? (reset($raw) ?: '')) : (string)($raw ?? '');
                    }
                    $vName = $vName ?: __('messages.Variant');

                    // value label
                    if ($val && method_exists($val, 'getTranslation')) $valName = $val->getTranslation('name', $loc, true);
                    else {
                    $raw = $val->name ?? null;
                    $valName = is_array($raw) ? ($raw[$loc] ?? (reset($raw) ?: '')) : (string)($raw ?? '');
                    }
                    $valName = $valName ?: __('messages.Value');

                    return $vName.': '.$valName;
                    })->join(' | ');
                    @endphp

                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td class="text-muted">{{ $labels ?: __('(No selections)') }}</td>
                        <td>{{ number_format((float)$item->price, 2) }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ $item->created_at?->format('Y-m-d H:i') }}</td>
                        <td class="text-end">
                            <a href="{{ route('products-variant.show', [$product, $item]) }}" class="btn btn-sm btn-outline-secondary me-1" title="{{ __('messages.show') }}">
                                <i class="fa-regular fa-eye"></i>
                            </a>
                            <a href="{{ route('products-variant.edit', [$product, $item]) }}" class="btn btn-sm btn-primary me-1" title="{{ __('messages.edit') }}">
                                <i class="fa-regular fa-pen-to-square"></i>
                            </a>
                            <form action="{{ route('products-variant.destroy', [$product, $item]) }}" method="POST" class="d-inline-block">
                                @csrf @method('DELETE')
                                <button onclick="return confirm('{{ __('messages.confirm_delete') }}')" class="btn btn-sm btn-danger" title="{{ __('messages.delete') }}">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">{{ __('messages.no_data') }}</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(isset($items) && $items instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div class="card-body">
            {{ $items->withQueryString()->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</div>
@endsection