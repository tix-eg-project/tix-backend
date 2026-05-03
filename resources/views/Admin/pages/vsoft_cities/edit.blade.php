@extends('Admin.layout.app')
@section('title', __('messages.map_city_to_zone'))

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
  <div class="col-12 col-lg-8 mx-auto">
    <div class="card-clean p-4">
      <div class="d-flex justify-content-between align-items-center mb-2">
        <h5 class="mb-0">{{ __('messages.map_city_to_zone') }}</h5>
        <a href="{{ route('admin.vsoft-cities.index') }}" class="btn btn-outline-secondary btn-sm">
          <i class="bi bi-arrow-left"></i> {{ __('messages.back') }}
        </a>
      </div>
      <hr class="mt-2">

      <div class="mb-3">
        <strong>{{ __('messages.city') }}:</strong>
        {{ $city->name }} <span class="text-muted">(#{{ $city->id }}, VSoft: {{ $city->vsoft_city_id }})</span>
      </div>

      <form method="post" action="{{ route('admin.vsoft-cities.update',$city->id) }}">
        @csrf
        <div class="mb-3">
          <label class="form-label">{{ __('messages.local_shipping_zone') }}</label>
          <select name="shipping_zone_id" class="form-select @error('shipping_zone_id') is-invalid @enderror">
            <option value="">{{ __('messages.none') }}</option>
            @foreach($zones as $z)
            @php
            $loc = app()->getLocale();
            $zName = $z->name ?? '—';
            if (is_array($zName)) { $zName = $zName[$loc] ?? reset($zName); }
            @endphp
            <option value="{{ $z->id }}" @selected($city->shipping_zone_id==$z->id)>{{ $z->id }} — {{ $zName }}</option>
            @endforeach
          </select>
          @error('shipping_zone_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
        </div>

        <div class="text-end">
          <button type="submit" class="btn btn-primary">
            <i class="bi bi-save"></i> {{ __('messages.save') }}
          </button>
        </div>
      </form>

    </div>
  </div>
</div>
@endsection