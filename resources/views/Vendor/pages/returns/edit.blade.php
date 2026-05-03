@extends('Vendor.layout.app')

@section('title', __('messages.returns'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">{{ __('messages.edit') }} — {{ __('messages.returns') }} #{{ $req->id }}</h4>
    @if($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
    <div>
        <a href="{{ route('vendor.returns.show', $req->id) }}" class="btn btn-outline-secondary">{{ __('messages.back') }}</a>
    </div>
</div>

<div class="row g-3">
    <div class="col-12 col-lg-6">
        <div class="card rounded-4 border-0 shadow-sm">
            <div class="card-body">
                <h5 class="mb-3">{{ __('messages.update') }}</h5>
                <form method="POST" action="{{ route('vendor.returns.update', $req->id) }}">
                    @csrf @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.status') }}</label>
                        <select name="new_status" id="new_status" class="form-select" required>
                            <option value="{{ \App\Enums\ReturnStatusEnum::UnderReturn->value }}"
                                @selected(old('new_status', $req->status?->value) == \App\Enums\ReturnStatusEnum::UnderReturn->value)>
                                {{ __('messages.under_return') }}
                            </option>
                            <option value="{{ \App\Enums\ReturnStatusEnum::ReceivedGood->value }}"
                                @selected(old('new_status', $req->status?->value) == \App\Enums\ReturnStatusEnum::ReceivedGood->value)>
                                {{ __('messages.received_good') }}
                            </option>
                            <option value="{{ \App\Enums\ReturnStatusEnum::ReceivedDefective->value }}"
                                @selected(old('new_status', $req->status?->value) == \App\Enums\ReturnStatusEnum::ReceivedDefective->value)>
                                {{ __('messages.received_defective') }}
                            </option>
                            <option value="{{ \App\Enums\ReturnStatusEnum::Rejected->value }}"
                                @selected(old('new_status', $req->status?->value) == \App\Enums\ReturnStatusEnum::Rejected->value)>
                                {{ __('messages.rejected') }}
                            </option>
                        </select>

                        @error('new_status')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>

                    <div class="row g-2">
                        <div class="col">
                            <label class="form-label">Approved at</label>
                            <input type="datetime-local" name="approved_at" class="form-control" value="{{ old('approved_at') }}">
                        </div>
                        <div class="col">
                            <label class="form-label">Received at</label>
                            <input type="datetime-local" name="received_at" class="form-control" value="{{ old('received_at') }}">
                        </div>
                        <div class="col">
                            <label class="form-label">Refunded at</label>
                            <input type="datetime-local" name="refunded_at" class="form-control" value="{{ old('refunded_at') }}">
                        </div>
                    </div>

                    <div class="row g-2 mt-2">

                        <div class="col">
                            <label class="form-label">{{ __('messages.refund_shipping') }}</label>
                            <input type="number" step="1" name="refund_shipping" class="form-control" placeholder="0.00" value="{{ old('refund_shipping') }}">
                        </div>
                    </div>

                    <div class="row g-2 mt-2">

                        <div class="col">
                            <label class="form-label">{{ __('messages.wallet_phone') }}</label>
                            <input type="text" name="payout_wallet_phone" class="form-control" value="{{ old('payout_wallet_phone', $req->payout_wallet_phone) }}">
                        </div>
                    </div>
                    <div class="row g-2 mt-2">

                        <div class="col">
                            <label class="form-label">{{ __('messages.quantity') }}</label>
                            <input type="number" name="quantity" class="form-control" value="{{ old('quantity', $req->quantity) }}" min="1"}}">
                        </div>
                    </div>



                    <button class="btn btn-primary mt-3">{{ __('messages.save') }}</button>
                </form>
            </div>
        </div>
    </div>

    {{-- كارت معلومات مختصر على اليمين --}}
    <div class="col-12 col-lg-6">
        <div class="card rounded-4 border-0 shadow-sm">
            <div class="card-body">
                <h5 class="mb-3">{{ __('messages.details') }}</h5>
                <dl class="row mb-0">
                    <dt class="col-sm-5">#ID</dt>
                    <dd class="col-sm-7">{{ $req->id }}</dd>
                    <dt class="col-sm-5">{{ __('messages.order') }}</dt>
                    <dd class="col-sm-7">#{{ $req->order_id }}</dd>
                    <dt class="col-sm-5">{{ __('messages.status') }}</dt>
                    <dd class="col-sm-7">{{ $req->status_label }}</dd>
                    <dt class="col-sm-5">{{ __('messages.quantity') }}</dt>
                    <dd class="col-sm-7">{{ $req->quantity }}</dd>
                    <dt class="col-sm-5">{{ __('messages.user') }}</dt>
                    <dd class="col-sm-7">{{ $req->user?->name ?? '-' }}</dd>
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection