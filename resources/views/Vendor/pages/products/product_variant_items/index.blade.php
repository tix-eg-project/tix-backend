{{-- resources/views/Vendor/pages/products/product_variant_items/index.blade.php --}}
@extends('Vendor.layout.app')

@section('title', __('messages.Sub Products'))

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4 text-center">{{ __('messages.Sub Products') }}</h4>

        <div class="card">
            <div class="card-body">
                @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <div class="table-responsive text-nowrap">
                    <table class="table table-striped text-black text-center">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ __('messages.Selections') }}</th>
                                <th>{{ __('messages.Price') }}</th>
                                <th>{{ __('messages.Quantity') }}</th>
                                <th>{{ __('messages.Created At') }}</th>
                                <th>{{ __('messages.Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse(($items ?? $product->variantItems()->latest('id')->get()) as $item)
                            @php
                            $pairs = collect($item->selections ?? []);
                            $variantIds = $pairs->map(fn($r) => $r['product_variant_id'] ?? $r['variant_id'] ?? null)->filter()->unique();
                            $valueIds = $pairs->map(fn($r) => $r['product_variant_value_id'] ?? $r['value_id'] ?? null)->filter()->unique();
                            $variants = \App\Models\ProductVariant::whereIn('id', $variantIds)->get()->keyBy('id');
                            $values = \App\Models\ProductVariantValue::whereIn('id', $valueIds)->get()->keyBy('id');
                            $loc = app()->getLocale();

                            $labels = $pairs->map(function($p) use ($variants, $values, $loc) {
                            $variantId = $p['product_variant_id'] ?? $p['variant_id'] ?? null;
                            $valueId = $p['product_variant_value_id'] ?? $p['value_id'] ?? null;

                            $v = $variantId ? ($variants[$variantId] ?? null) : null;
                            $val = $valueId ? ($values[$valueId] ?? null) : null;

                            // Variant name
                            if ($v && method_exists($v, 'getTranslation')) {
                            $vName = $v->getTranslation('name', $loc, true);
                            } else {
                            $raw = $v->name ?? null;
                            $vName = is_array($raw) ? ($raw[$loc] ?? (reset($raw) ?: '')) : (string)($raw ?? '');
                            }
                            $vName = $vName ?: 'Variant';

                            // Value name
                            if ($val && method_exists($val, 'getTranslation')) {
                            $valName = $val->getTranslation('name', $loc, true);
                            } else {
                            $raw = $val->name ?? null;
                            $valName = is_array($raw) ? ($raw[$loc] ?? (reset($raw) ?: '')) : (string)($raw ?? '');
                            }
                            $valName = $valName ?: 'Value';

                            return $vName . ': ' . $valName;
                            })->join(' | ');
                            @endphp

                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $labels ?: __('(No selections)') }}</td>
                                <td>{{ number_format((float)$item->price, 2) }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>{{ $item->created_at?->format('Y-m-d H:i') }}</td>
                                <td class="text-nowrap">
                                    <a href="{{ route('vendor.products-variant.show', [$product, $item]) }}" class="btn btn-sm btn-outline-secondary me-1">
                                        <i class="fa-regular fa-eye"></i>
                                    </a>
                                    <a href="{{ route('vendor.products-variant.edit', [$product, $item]) }}" class="btn btn-sm btn-primary me-1">
                                        <i class="fa-regular fa-pen-to-square"></i>
                                    </a>
                                    <form action="{{ route('vendor.products-variant.destroy', [$product, $item]) }}" method="POST" class="d-inline-block">
                                        @csrf @method('DELETE')
                                        <button onclick="return confirm('{{ __('messages.confirm_delete') }}')" class="btn btn-sm btn-danger">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center">{{ __('messages.no_data') }}</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>

    </div>
</div>
@endsection