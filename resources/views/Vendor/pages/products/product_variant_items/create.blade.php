{{-- resources/views/Admin/pages/products/product_variant_items/create.blade.php --}}
@extends('Vendor.layout.app')

@section('title', __('messages.Add Sub Product'))

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4 text-center">
            {{ __('messages.Add Sub Product') }}
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

                <form method="POST" action="{{ route('vendor.products-variant.store', $product) }}">
                    @csrf

                    @php $loc = app()->getLocale(); @endphp

                    {{-- Variants & Values --}}
                    @forelse($variants as $variant)
                    @php
                    // اسم الفاريانت (Spatie or JSON)
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
                                    value="" checked>
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
                                    value="{{ $value->id }}">
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
                            value="{{ old('price') }}" placeholder="0.00" required>
                        @error('price') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    {{-- Quantity --}}
                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.Quantity') }}</label>
                        <input type="number" name="quantity" min="0"
                            class="form-control @error('quantity') is-invalid @enderror"
                            value="{{ old('quantity', 0) }}" placeholder="0" required>
                        @error('quantity') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    <button type="submit" class="btn btn-primary">{{ __('messages.save') }}</button>
                    <a href="{{ route('vendor.products-variant.index', $product) }}" class="btn btn-secondary">
                        {{ __('messages.cancel') }}
                    </a>
                </form>

            </div>
        </div>

    </div>
</div>
@endsection