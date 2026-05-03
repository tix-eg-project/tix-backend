@extends('Vendor.layout.app')

@section('title', __('messages.returns'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">{{ __('messages.returns') }}</h4>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('messages.close') }}"></button>
</div>
@endif

@php
/**
* تطبيع $statusOptions عشان نتعامل مع أي شكل (Array/Enum/Int)
* ونعرض value/label دايمًا.
*/
$statusOptions = collect($statusOptions ?? \App\Enums\ReturnStatusEnum::cases())
->map(function ($st) {
if (is_array($st)) {
return [
'value' => $st['value'] ?? null,
'label' => $st['label'] ?? (string)($st['value'] ?? ''),
];
}
if ($st instanceof \App\Enums\ReturnStatusEnum) {
return ['value' => $st->value, 'label' => $st->label()];
}
$v = (int) $st;
return ['value' => $v, 'label' => \App\Enums\ReturnStatusEnum::tryFrom($v)?->label() ?? (string)$v];
})
->filter(fn($x) => !is_null($x['value']))
->values();
@endphp

<div class="card rounded-4 border-0 shadow-sm mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('vendor.returns.index') }}">
            <div class="row g-3 align-items-end">
                <div class="col-12 col-md-3">
                    <label class="form-label">{{ __('messages.search') }}</label>
                    <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="#ID / {{ __('messages.user') }} / {{ __('messages.phone') }}">
                </div>

                <div class="col-6 col-md-2">
                    <label class="form-label">{{ __('messages.status') }}</label>
                    <select name="status" class="form-select" onchange="this.form.submit()">
                        <option value="">{{ __('messages.all') }}</option>
                        @foreach($statusOptions as $st)
                        <option value="{{ $st['value'] }}" @selected(request('status')===(string)$st['value'])>
                            {{ $st['label'] }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-6 col-md-2">
                    <label class="form-label">{{ __('messages.created_at') }} ({{ __('messages.from') }})</label>
                    <input type="date" name="created_from" class="form-control" value="{{ request('created_from') }}">
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label">{{ __('messages.created_at') }} ({{ __('messages.to') }})</label>
                    <input type="date" name="created_to" class="form-control" value="{{ request('created_to') }}">
                </div>

                <div class="col-6 col-md-2">
                    <label class="form-label">{{ __('messages.approved_at') }} ({{ __('messages.from') }})</label>
                    <input type="date" name="approved_from" class="form-control" value="{{ request('approved_from') }}">
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label">{{ __('messages.approved_at') }} ({{ __('messages.to') }})</label>
                    <input type="date" name="approved_to" class="form-control" value="{{ request('approved_to') }}">
                </div>

                <div class="col-6 col-md-2">
                    <label class="form-label">{{ __('messages.received_at') }} ({{ __('messages.from') }})</label>
                    <input type="date" name="received_from" class="form-control" value="{{ request('received_from') }}">
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label">{{ __('messages.received_at') }} ({{ __('messages.to') }})</label>
                    <input type="date" name="received_to" class="form-control" value="{{ request('received_to') }}">
                </div>

                <div class="col-6 col-md-2">
                    <label class="form-label">{{ __('messages.refunded_at') }} ({{ __('messages.from') }})</label>
                    <input type="date" name="refunded_from" class="form-control" value="{{ request('refunded_from') }}">
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label">{{ __('messages.refunded_at') }} ({{ __('messages.to') }})</label>
                    <input type="date" name="refunded_to" class="form-control" value="{{ request('refunded_to') }}">
                </div>

                <div class="col-12 col-md-2 text-end">
                    <button class="btn btn-secondary w-100">
                        <i class="bx bx-filter-alt"></i> {{ __('messages.filter') }}
                    </button>
                </div>

                @if(collect(request()->only([
                'search','status','created_from','created_to',
                'approved_from','approved_to',
                'received_from','received_to',
                'refunded_from','refunded_to'
                ]))->filter()->isNotEmpty())
                <div class="col-12 col-md-2">
                    <a href="{{ route('vendor.returns.index') }}" class="btn btn-outline-secondary w-100">
                        {{ __('messages.clear') }}
                    </a>
                </div>
                @endif
            </div>
        </form>
    </div>
</div>

<div class="card rounded-4 custom-card bg-light text-dark border-0 shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ __('messages.order') }}</th>
                        <th>{{ __('messages.user') }}</th>
                        <th>{{ __('messages.quantity') }}</th>
                        <th>{{ __('messages.status') }}</th>
                        <th>{{ __('messages.created_at') }}</th>
                        <th>{{ __('messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($returns as $r)
                    <tr>
                        <td>{{ $r->id }}</td>
                        <td>#{{ $r->order_id }}</td>
                        <td>{{ $r->user?->name ?? '-' }}</td>
                        <td>{{ $r->quantity }}</td>
                        <td>{{ $r->status_label }}</td>
                        <td>{{ optional($r->created_at)->format('Y-m-d H:i') }}</td>
                        <td>
                            <a href="{{ route('vendor.returns.show', $r->id) }}"
                                class="btn btn-sm btn-info"
                                title="{{ __('messages.details') }}">
                                <i class="fa-regular fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted">{{ __('messages.no_data') }}</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center mt-3">
            {{ $returns->appends(request()->query())->links('pagination::bootstrap-4') }}
        </div>
    </div>
</div>
@endsection