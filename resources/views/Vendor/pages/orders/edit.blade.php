@extends('Vendor.layout.app')
@section('title', __('messages.edit'))

@section('content')
<div class="content-wrapper">
  <div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h4 class="mb-0">{{ __('messages.edit') }}</h4>
      <a href="{{ route('vendor.orders.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> {{ __('messages.back') ?? 'Back' }}
      </a>
    </div>

    <div class="card rounded-4 custom-card bg-light text-dark border-0 shadow-sm">
      <div class="card-body">
        <form method="POST" action="{{ route('vendor.orders.update', $order->id) }}">
          @csrf @method('PUT')

          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">{{ __('messages.payment_method') }}</label>
              <input type="text" class="form-control" value="{{ $order->payment_method_name ?? '-' }}" disabled>
            </div>

            <div class="col-md-6">
              <label class="form-label">{{ __('messages.total') }}</label>
              @php
              $orderTotal = $order->items->sum(fn($i) => (float)$i->price_after * (int)$i->quantity);
              @endphp
              <input type="text" class="form-control" value="{{ number_format($orderTotal, 2) }} {{ __('messages.currency') }}" disabled>
            </div>

            <div class="col-md-6">
              <label class="form-label">{{ __('messages.status') }}</label>
              <select name="status" class="form-select @error('status') is-invalid @enderror">
                @foreach($statuses as $status)
                <option value="{{ $status->value }}" @selected($status->value === $order->status)>{{ $status->value }}</option>
                @endforeach
              </select>
              @error('status') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-6">
              <label class="form-label">{{ __('messages.delivery_date') }}</label>
              <input type="date" name="delivery_date"
                class="form-control @error('delivery_date') is-invalid @enderror"
                value="{{ old('delivery_date', optional($order->delivered_at)->format('Y-m-d')) }}">
              @error('delivery_date') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>
          </div>

          <div class="mt-4 d-flex gap-2">
            <button type="submit" class="btn btn-primary">
              <i class="bi bi-check-circle"></i> {{ __('messages.save') }}
            </button>
            <a href="{{ route('vendor.orders.index') }}" class="btn btn-outline-secondary">
              {{ __('messages.cancel') }}
            </a>
          </div>
        </form>
      </div>
    </div>

  </div>
</div>
@endsection