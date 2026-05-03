@extends('Vendor.layout.app')
@section('title', __('messages.Add Variant'))

@push('styles')
<style>
    .card-clean {
        border: 1px solid rgba(0, 0, 0, .08);
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, .06);
        background: #fff;
    }
</style>
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="col-12 col-xl-8 mx-auto">
        <div class="card-clean p-4">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h5 class="mb-0">{{ __('messages.Add Variant') }}</h5>
                <a href="{{ route('vendor.variants.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left"></i> {{ __('messages.back') ?? __('messages.cancel') }}
                </a>
            </div>
            <hr class="mt-2">

            @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                </ul>
            </div>
            @endif

            <form method="POST" action="{{ route('vendor.variants.store') }}">
                @csrf

                @foreach (LaravelLocalization::getSupportedLocales() as $localeCode => $locale)
                <div class="mb-3">
                    <label class="form-label">{{ __('messages.name') }} ({{ strtoupper($localeCode) }})</label>
                    <input
                        type="text"
                        name="name[{{ $localeCode }}]"
                        class="form-control @error('name.'.$localeCode) is-invalid @enderror"
                        value="{{ old('name.'.$localeCode) }}">
                    @error('name.'.$localeCode)
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                @endforeach

                <div class="mt-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check2-circle me-1"></i>{{ __('messages.save') }}
                    </button>
                    <a href="{{ route('vendor.variants.index') }}" class="btn btn-secondary">
                        <i class="bi bi-x-circle me-1"></i>{{ __('messages.cancel') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection