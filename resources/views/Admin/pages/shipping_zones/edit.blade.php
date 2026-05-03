@extends('Admin.layout.app')
@section('title', __('messages.edit_shipping_zone'))

@push('styles')
<style>
    .card-clean {
        border: 1px solid rgba(0, 0, 0, .08);
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, .06);
        background: #fff
    }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-12 col-lg-10 mx-auto">
        <div class="card-clean p-4">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h5 class="mb-0">{{ __('messages.edit_shipping_zone') }}</h5>
                <a href="{{ route('admin.shipping_zones.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left"></i> {{ __('messages.back') }}
                </a>
            </div>
            <hr class="mt-2">

            <form action="{{ route('admin.shipping_zones.update', $shippingZone->id) }}" method="POST">
                @csrf @method('PUT')

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">{{ __('messages.name_ar') }}</label>
                        <input type="text" name="name_ar"
                            class="form-control @error('name_ar') is-invalid @enderror"
                            value="{{ old('name_ar', $shippingZone->getTranslation('name','ar')) }}"
                            placeholder="{{ __('messages.enter_name_ar') }}">
                        @error('name_ar') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">{{ __('messages.name_en') }}</label>
                        <input type="text" name="name_en"
                            class="form-control @error('name_en') is-invalid @enderror"
                            value="{{ old('name_en', $shippingZone->getTranslation('name','en')) }}"
                            placeholder="{{ __('messages.enter_name_en') }}">
                        @error('name_en') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">{{ __('messages.price') }}</label>
                        <div class="input-group">
                            <input type="number" step="0.01" name="price"
                                class="form-control @error('price') is-invalid @enderror"
                                value="{{ old('price', $shippingZone->price) }}"
                                placeholder="{{ __('messages.enter_shipping_price') }}">
                            <span class="input-group-text">{{ __('messages.currency') }}</span>
                        </div>
                        @error('price') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="text-end mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> {{ __('messages.update') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection