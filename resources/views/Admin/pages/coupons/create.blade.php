@extends('Admin.layout.app')
@section('title', __('messages.add_coupon'))

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
<div class="card-clean p-4">
    <h5 class="mb-3">{{ __('messages.add_coupon') }}</h5>

    @if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('admin.coupons.store') }}">
        @csrf

        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">{{ __('messages.code') }}</label>
                <input type="text" name="code" class="form-control @error('code') is-invalid @enderror"
                    value="{{ old('code') }}" required>
                @error('code') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-4">
                <label class="form-label">{{ __('messages.discount_type') }}</label>
                <select name="discount_type" class="form-control @error('discount_type') is-invalid @enderror">
                    <option value="percent" @selected(old('discount_type')=='percent' )>{{ __('messages.percent') }}</option>
                    <option value="amount" @selected(old('discount_type')=='amount' )>{{ __('messages.amount') }}</option>
                </select>
                @error('discount_type') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-4">
                <label class="form-label">{{ __('messages.discount_value') }}</label>
                <input type="number" step="0.01" name="discount_value"
                    class="form-control @error('discount_value') is-invalid @enderror"
                    value="{{ old('discount_value') }}" required>
                @error('discount_value') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-4">
                <label class="form-label">{{ __('messages.usage_limit') }} ({{ __('messages.optional') }})</label>
                <input type="number" min="0" name="max_uses" class="form-control @error('max_uses') is-invalid @enderror"
                    value="{{ old('max_uses') }}" placeholder="∞">
                @error('max_uses') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-4">
                <label class="form-label">{{ __('messages.starts_at') }}</label>
                <input type="date" name="starts_at" class="form-control @error('starts_at') is-invalid @enderror"
                    value="{{ old('starts_at') }}" required>
                @error('starts_at') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-4">
                <label class="form-label">{{ __('messages.expiry_date') }}</label>
                <input type="date" name="ends_at" class="form-control @error('ends_at') is-invalid @enderror"
                    value="{{ old('ends_at') }}" required>
                @error('ends_at') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>

            <div class="col-12">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active"
                        {{ old('is_active', true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">{{ __('messages.is_active') }}</label>
                </div>
            </div>
        </div>

        <div class="mt-3 d-flex gap-2">
            <button type="submit" class="btn btn-success">{{ __('messages.save') }}</button>
            <a href="{{ route('admin.coupons.index') }}" class="btn btn-secondary">{{ __('messages.cancel') }}</a>
        </div>
    </form>
</div>
@endsection