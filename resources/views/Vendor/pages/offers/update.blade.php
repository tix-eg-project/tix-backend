@extends('Vendor.layout.app')
@section('title', __('messages.edit_offer'))

@push('styles')
<style>
    .card-clean {
        border: 1px solid rgba(0, 0, 0, .08);
        border-radius: 16px;
        background: #fff;
        box-shadow: 0 10px 30px rgba(0, 0, 0, .06);
    }
</style>
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="col-12 col-xl-10 mx-auto">
        <div class="card-clean p-4">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h5 class="mb-0">{{ __('messages.edit_offer') }}</h5>
                <a href="{{ route('vendor.offers.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left"></i> {{ __('messages.back') }}
                </a>
            </div>
            <hr class="mt-2">

            @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
            @endif

            @php
            $startVal = old('start_date') ?? ($offer?->start_date ? \Illuminate\Support\Carbon::parse($offer->start_date)->format('Y-m-d') : '');
            $endVal = old('end_date') ?? ($offer?->end_date ? \Illuminate\Support\Carbon::parse($offer->end_date)->format('Y-m-d') : '');
            $selectedProducts = collect(old('products', $offer?->products?->pluck('id')->toArray() ?? []))->map(fn($v)=>(int)$v)->all();
            @endphp

            <form action="{{ route('vendor.offers.update', $offer) }}" method="POST">
                @csrf @method('PUT')

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Name (Arabic)</label>
                        <input type="text" name="name[ar]" class="form-control @error('name.ar') is-invalid @enderror"
                            value="{{ old('name.ar', $offer?->getTranslation('name','ar')) }}" required>
                        @error('name.ar') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Name (English)</label>
                        <input type="text" name="name[en]" class="form-control @error('name.en') is-invalid @enderror"
                            value="{{ old('name.en', $offer?->getTranslation('name','en')) }}" required>
                        @error('name.en') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row g-3 mt-1">
                    <div class="col-md-4">
                        <label class="form-label">{{ __('messages.Amount Value') }}</label>
                        <input type="number" step="0.01" name="amount_value"
                            class="form-control @error('amount_value') is-invalid @enderror"
                            value="{{ old('amount_value', $offer->amount_value) }}" required>
                        @error('amount_value') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">{{ __('messages.Amount Type') }}</label>
                        <select name="amount_type" class="form-select @error('amount_type') is-invalid @enderror" required>
                            <option value="1" @selected((int)old('amount_type', $offer->amount_type)===1)>{{ __('messages.percent') }}</option>
                            <option value="2" @selected((int)old('amount_type', $offer->amount_type)===2)>{{ __('messages.fixed') }}</option>
                        </select>
                        @error('amount_type') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">{{ __('messages.Start Date') }}</label>
                        <input type="date" name="start_date" class="form-control @error('start_date') is-invalid @enderror"
                            value="{{ $startVal }}">
                        @error('start_date') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">{{ __('messages.End Date') }}</label>
                        <input type="date" name="end_date" class="form-control @error('end_date') is-invalid @enderror"
                            value="{{ $endVal }}">
                        @error('end_date') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="mt-3">
                    <label class="form-label">{{ __('menu.Select products') }}</label>
                    <select name="products[]" class="form-select @error('products') is-invalid @enderror" multiple required>
                        @foreach ($products as $product)
                        <option value="{{ $product->id }}" @selected(in_array($product->id, $selectedProducts))>
                            {{ $product->name_text ?? $product->name ?? ('#'.$product->id) }}
                        </option>
                        @endforeach
                    </select>
                    @error('products') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">{{ __('messages.update') }}</button>
                    <a href="{{ route('vendor.offers.index') }}" class="btn btn-secondary">{{ __('messages.cancel') }}</a>
                </div>
            </form>

        </div>
    </div>
</div>
@endsection