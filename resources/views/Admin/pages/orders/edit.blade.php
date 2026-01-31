@extends('Admin.layout.app')
@section('title', __('messages.edit'))

@section('content')
<div class="card rounded-4 custom-card bg-light text-dark border-0 shadow-sm">
  <div class="card-body">
    <h4 class="card-title mb-3">{{ __('messages.edit') }}</h4>

    @if ($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
    @endif

    <form method="POST" action="{{ route('admin.orders.update', $order->id) }}">
      @csrf @method('PUT')

      <div class="row g-3">
        <div class="col-md-4">
          <label class="form-label">{{ __('messages.order_id') }}</label>
          <input type="text" class="form-control" value="#{{ $order->id }}" disabled>
        </div>

        <div class="col-md-4">
          <label class="form-label">{{ __('messages.payment_method') }}</label>
          <input type="text" class="form-control" value="{{ $order->payment_method_name ?? '-' }}" disabled>
        </div>

        <div class="col-md-4">
          <label class="form-label">{{ __('messages.total') }}</label>
          <input type="text" class="form-control"
            value="{{ number_format($order->total, 2) }} {{ __('messages.currency') }}" disabled>
        </div>

        <div class="col-md-4">
          <label class="form-label">{{ __('messages.status') }}</label>
          <select name="status" class="form-select @error('status') is-invalid @enderror" required>
            @foreach($statuses as $status)
            <option value="{{ $status->value }}" @selected($status->value === $order->status)>{{ $status->value }}</option>
            @endforeach
          </select>
          @error('status') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-4">
          <label class="form-label">{{ __('messages.delivery_date') }}</label>
          <input type="date" name="delivery_date"
            class="form-control @error('delivery_date') is-invalid @enderror"
            value="{{ old('delivery_date', optional($order->delivered_at)->format('Y-m-d')) }}">
          @error('delivery_date') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
        </div>
      </div>

      <div class="mt-3 d-flex gap-2">
        <button type="submit" class="btn btn-primary">{{ __('messages.save') }}</button>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">{{ __('messages.cancel') }}</a>
      </div>
    </form>
  </div>
</div>
@endsection