{{-- resources/views/Admin/pages/products/product_variant_items/edit.blade.php --}}
@extends('Vendor.layout.app')

@section('title', __('messages.Edit Sub Product'))

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4 text-center">
            {{ __('messages.Edit Sub Product') }}
        </h4>

        <div class="card">
            <div class="card-body">

                @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form method="POST" action="{{ route('vendor.products-variant.update', [$product, $item]) }}">
                    @csrf @method('PUT')

                    @php
                    // ندعم الشكلين: product_variant_id/value_id وأيضًا variant_id/value_id
                    $current = collect($item->selections ?? [])
                    ->mapWithKeys(function ($row) {
                    $vk = $row['product_variant_id'] ?? $row['variant_id'] ?? null;
                    $vv = $row['product_variant_value_id'] ?? $row['value_id'] ?? null;
                    return $vk !== null ? [ (int)$vk => ($vv !== null ? (int)$vv : null) ] : [];
                    });

                    $loc = app()->getLocale();
                    @endphp

                    {{-- Variants & Values --}}
                    @forelse($variants as $variant)
                    @php
                    if (method_exists($variant, 'getTranslation')) {
                    $vName = $variant->getTranslation('name', $loc, true);
                    } else {
                    $raw = $variant->name;
                    $vName = is_array($raw) ? ($raw[$loc] ?? (reset($raw) ?: '')) : (string)$raw;
                    }
                    $vName = $vName ?: __('messages.Variant');
                    @endphp

                    <div class="mb-3 p-3 border rounded">
                        <label class="form-label fw-bold mb-2">{{ $vName }}</label>

                        <div class="d-flex flex-wrap" style="gap:12px;">
                            {{-- No selection --}}
                            <div class="form-check">
                                <input class="form-check-input" type="radio"
                                    name="variant_values[{{ $variant->id }}]"
                                    id="variant-{{ $variant->id }}-none"
                                    value=""
                                    {{ ! $current->has($variant->id) ? 'checked' : '' }}>
                                <label class="form-check-label" for="variant-{{ $variant->id }}-none">
                                    {{ __('messages.No selection') }}
                                </label>
                            </div>

                            {{-- Values --}}
                            @foreach($variant->values as $value)
                            @php
                            if (method_exists($value, 'getTranslation')) {
                            $valName = $value->getTranslation('name', $loc, true);
                            } else {
                            $raw = $value->name;
                            $valName = is_array($raw) ? ($raw[$loc] ?? (reset($raw) ?: '')) : (string)$raw;
                            }
                            $valName = $valName ?: __('messages.Value');
                            @endphp

                            <div class="form-check">
                                <input class="form-check-input" type="radio"
                                    name="variant_values[{{ $variant->id }}]"
                                    id="variant-{{ $variant->id }}-value-{{ $value->id }}"
                                    value="{{ $value->id }}"
                                    {{ ((int)$current->get($variant->id) === (int)$value->id) ? 'checked' : '' }}>
                                <label class="form-check-label" for="variant-{{ $variant->id }}-value-{{ $value->id }}">
                                    {{ $valName }}
                                </label>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @empty
                    <div class="alert alert-warning mb-3">{{ __('messages.no_data') }}</div>
                    @endforelse

                    {{-- Price --}}
                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.Price') }}</label>
                        <input type="number" name="price" step="0.01" min="0"
                            class="form-control @error('price') is-invalid @enderror"
                            value="{{ old('price', $item->price) }}" placeholder="0.00">
                        @error('price') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    {{-- Quantity --}}
                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.Quantity') }}</label>
                        <input type="number" name="quantity" min="0"
                            class="form-control @error('quantity') is-invalid @enderror"
                            value="{{ old('quantity', $item->quantity) }}" placeholder="0">
                        @error('quantity') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    <button type="submit" class="btn btn-primary">{{ __('messages.update') }}</button>
                    <a href="{{ route('vendor.products-variant.index', $product) }}" class="btn btn-secondary">
                        {{ __('messages.cancel') }}
                    </a>
                </form>

            </div>
        </div>

    </div>
</div>
@endsection