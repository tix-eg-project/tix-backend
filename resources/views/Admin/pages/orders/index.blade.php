@extends('Admin.layout.app')

@section('orders_active', 'active')
@section('title', __('messages.orders'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">{{ __('messages.orders') }}</h4>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('messages.close') }}"></button>
</div>
@endif

{{-- بحث + فلاتر --}}
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.orders.index') }}" id="searchForm">
            <div class="row g-2 align-items-end">

                <div class="col-12 col-md-3">
                    <label class="form-label">{{ __('messages.search') }}</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bx bx-search"></i></span>
                        <input type="text" name="search" class="form-control"
                            placeholder="{{ __('messages.search') }}"
                            value="{{ request('search') }}">
                    </div>
                </div>

                <div class="col-6 col-md-2">
                    <label class="form-label">{{ __('messages.status') }}</label>
                    <select name="status" class="form-select" onchange="this.form.submit()">
                        <option value="">{{ __('messages.all') }}</option>
                        @foreach(($statusOptions ?? []) as $st)
                        <option value="{{ $st }}" @selected(request('status')===$st)>{{ $st }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-6 col-md-2">
                    <label class="form-label">{{ __('messages.payment_method') }}</label>
                    <select name="payment_method" class="form-select" onchange="this.form.submit()">
                        <option value="">{{ __('messages.all') }}</option>
                        @foreach(($paymentOptions ?? []) as $pm)
                        <option value="{{ $pm }}" @selected(request('payment_method')===$pm)>{{ $pm }}</option>
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
                    <label class="form-label">{{ __('messages.delivery_date') }} ({{ __('messages.from') }})</label>
                    <input type="date" name="delivered_from" class="form-control" value="{{ request('delivered_from') }}">
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label">{{ __('messages.delivery_date') }} ({{ __('messages.to') }})</label>
                    <input type="date" name="delivered_to" class="form-control" value="{{ request('delivered_to') }}">
                </div>

                <div class="col-12 col-md-2 text-end">
                    <button class="btn btn-secondary w-100">
                        <i class="bx bx-filter-alt"></i> {{ __('messages.filter') }}
                    </button>
                </div>

                @if(collect(request()->only(['search','status','payment_method','created_from','created_to','delivered_from','delivered_to']))->filter()->isNotEmpty())
                <div class="col-12 col-md-2">
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary w-100">
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
            <table class="table table-striped table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>{{ __('messages.user') }}</th>
                        <th>{{ __('messages.status') }}</th>
                        <th>{{ __('messages.delivery_date') }}</th>
                        <th>{{ __('messages.payment_method') }}</th>
                        <th>{{ __('messages.total') }}</th>
                        <th>{{ __('messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                    <tr>
                        <td>{{ $order->id }}</td>
                        <td>{{ $order->user?->name ?? '-' }}</td>

                        {{-- Badge بسيطة للحالة --}}
                        @php
                        $badgeMap = [
                        'placed' => 'bg-secondary-subtle text-secondary border border-secondary-subtle',
                        'paid' => 'bg-success-subtle text-success border border-success-subtle',
                        'shipped' => 'bg-info-subtle text-info border border-info-subtle',
                        'delivered' => 'bg-primary-subtle text-primary border border-primary-subtle',
                        'canceled' => 'bg-danger-subtle text-danger border border-danger-subtle',
                        'pending' => 'bg-warning-subtle text-warning border border-warning-subtle',
                        ];
                        $stClass = $badgeMap[$order->status] ?? 'bg-secondary-subtle text-secondary border border-secondary-subtle';
                        @endphp
                        <td><span class="badge {{ $stClass }}">{{ $order->status }}</span></td>

                        <td>{{ optional($order->delivered_at)->format('Y-m-d') ?? '-' }}</td>
                        <td>{{ $order->payment_method_name ?? '-' }}</td>
                        <td>{{ number_format($order->total, 2) }} {{ __('messages.currency') }}</td>

                        <td class="text-end">
                            <a href="{{ route('admin.orders.edit', $order->id) }}"
                                class="btn btn-sm btn-primary"
                                title="{{ __('messages.edit') }}">
                                <i class="fa-regular fa-pen-to-square"></i>
                            </a>

                            <a href="{{ route('admin.orders.show', $order->id) }}"
                                class="btn btn-sm btn-info"
                                title="{{ __('messages.details') }}">
                                <i class="fa-regular fa-eye"></i>
                            </a>

                            <form method="POST"
                                action="{{ route('admin.orders.destroy', $order->id) }}"
                                class="d-inline-block">
                                @csrf @method('DELETE')
                                <button type="submit"
                                    class="btn btn-sm btn-danger"
                                    title="{{ __('messages.delete') }}"
                                    onclick="return confirm('{{ __('messages.are_you_sure') }}')">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
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
            {{ $orders->appends(request()->query())->links('pagination::bootstrap-4') }}
        </div>
    </div>
</div>
@endsection