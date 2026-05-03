@extends('Admin.layout.app')
@section('title', __('messages.coupons'))

@push('styles')
<style>
    .coupon-hero {
        border-radius: 16px;
        background: linear-gradient(135deg, #0ea5ea 0%, #6a5af9 100%);
        color: #fff;
        padding: 18px 20px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, .08)
    }

    .chip {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        padding: .35rem .7rem;
        border-radius: 999px;
        border: 1px solid rgba(255, 255, 255, .35);
        backdrop-filter: blur(4px);
        font-weight: 600
    }

    .card-clean {
        border: 1px solid rgba(0, 0, 0, .08);
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, .06);
        background: #fff
    }

    .table thead th {
        background: #f7f7fb !important;
        color: #424750;
        font-weight: 700;
        border: 0 !important
    }

    .table tbody td {
        vertical-align: middle;
        border-color: #efeff3 !important;
        color: #2b2b2b
    }

    .badge-soft {
        border-radius: 999px;
        padding: .35rem .65rem;
        font-weight: 700;
        border: 1px solid transparent
    }
</style>
@endpush

@section('content')
<div class="coupon-hero d-flex align-items-center justify-content-between gap-2 mb-3">
    <div class="d-flex flex-column">
        <h4 class="mb-1">{{ __('messages.coupons') }}</h4>
        <div class="d-flex gap-2 flex-wrap">
            <!-- <span class="chip"><i class="bi bi-ticket-perforated"></i> {{ $coupons->total() }} {{ __('messages.records') }}</span> -->
            <!-- <span class="chip"><i class="bi bi-filter"></i> {{ __('messages.search') }}: {{ request('search','—') }}</span> -->
        </div>
    </div>
    <a href="{{ route('admin.coupons.create') }}" class="btn btn-light text-dark">
        <i class="bx bx-plus me-1"></i> {{ __('messages.add_coupon') }}
    </a>
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
        <form method="GET" action="{{ route('admin.coupons.index') }}" id="searchForm">
            <div class="row g-2 align-items-end">
                <div class="col-12 col-md-4">
                    <label class="form-label">{{ __('messages.search') }}</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bx bx-search"></i></span>
                        <input type="text" name="search" class="form-control"
                            placeholder="{{ __('messages.search') }}"
                            value="{{ request('search') }}">
                        @if(request('search') || request('discount_type') || request('state') || request('start_from') || request('end_to'))
                        <a href="{{ route('admin.coupons.index') }}" class="btn btn-outline-secondary">
                            {{ __('messages.clear') }}
                        </a>
                        @endif
                    </div>
                </div>

                <div class="col-6 col-md-2">
                    <label class="form-label">{{ __('messages.discount_type') }}</label>
                    <select name="discount_type" class="form-select" onchange="this.form.submit()">
                        <option value="">{{ __('messages.all') }}</option>
                        <option value="percent" @selected(request('discount_type')==='percent' )>{{ __('messages.percent') }}</option>
                        <option value="amount" @selected(request('discount_type')==='amount' )>{{ __('messages.amount') }}</option>
                    </select>
                </div>

                <div class="col-6 col-md-2">
                    <label class="form-label">{{ __('messages.status') }}</label>
                    <select name="state" class="form-select" onchange="this.form.submit()">
                        <option value="">{{ __('messages.all') }}</option>
                        <option value="active" @selected(request('state')==='active' )>{{ __('messages.active') }}</option>
                        <option value="scheduled" @selected(request('state')==='scheduled' )>{{ __('messages.scheduled') }}</option>
                        <option value="expired" @selected(request('state')==='expired' )>{{ __('messages.expired') }}</option>
                        <option value="disabled" @selected(request('state')==='disabled' )>{{ __('messages.disabled') }}</option>
                    </select>
                </div>

                <div class="col-6 col-md-2">
                    <label class="form-label">{{ __('messages.starts_at') }}</label>
                    <input type="date" name="start_from" class="form-control" value="{{ request('start_from') }}">
                </div>

                <div class="col-6 col-md-2">
                    <label class="form-label">{{ __('messages.expiry_date') }}</label>
                    <input type="date" name="end_to" class="form-control" value="{{ request('end_to') }}">
                </div>

                <div class="col-12 col-md-2 text-end">
                    <button class="btn btn-secondary w-100">
                        <i class="bx bx-filter-alt"></i> {{ __('messages.filter') }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card-clean p-0">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th style="width:56px">#</th>
                    <th>{{ __('messages.code') }}</th>
                    <th>{{ __('messages.discount') }}</th>
                    <th>{{ __('messages.usage_limit') }}</th>
                    <th>{{ __('messages.validity') }}</th>
                    <th>{{ __('messages.status') }}</th>
                    <th class="text-end" style="width:160px">{{ __('messages.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($coupons as $coupon)
                @php
                $typeBadge = $coupon->discount_type === 'percent'
                ? 'badge-soft bg-primary-subtle text-primary border border-primary-subtle'
                : 'badge-soft bg-info-subtle text-info border border-info-subtle';

                $now = \Carbon\Carbon::now();
                $isDisabled = ! (bool) $coupon->is_active;
                $isScheduled = (bool) $coupon->is_active && $coupon->starts_at && $now->lt($coupon->starts_at);
                $isExpired = $coupon->ends_at && $now->gt($coupon->ends_at);
                $isActive = ((bool)$coupon->is_active
                && (!$coupon->starts_at || $now->gte($coupon->starts_at))
                && (!$coupon->ends_at || $now->lte($coupon->ends_at)));

                $statusBadge = 'badge-soft bg-secondary-subtle text-secondary border border-secondary-subtle';
                $statusLabel = __('messages.inactive');
                if ($isDisabled) { $statusBadge='badge-soft bg-secondary-subtle text-secondary border border-secondary-subtle'; $statusLabel=__('messages.disabled'); }
                elseif ($isScheduled){ $statusBadge='badge-soft bg-warning-subtle text-warning border border-warning-subtle'; $statusLabel=__('messages.scheduled'); }
                elseif ($isExpired) { $statusBadge='badge-soft bg-danger-subtle text-danger border border-danger-subtle'; $statusLabel=__('messages.expired'); }
                elseif ($isActive) { $statusBadge='badge-soft bg-success-subtle text-success border border-success-subtle'; $statusLabel=__('messages.active'); }

                $uses = $coupon->max_uses ? ($coupon->used_count . ' / ' . $coupon->max_uses) : '∞';
                @endphp
                <tr>
                    <td>{{ $loop->iteration + (($coupons->currentPage()-1)*$coupons->perPage()) }}</td>
                    <td><code class="fw-bold">{{ $coupon->code }}</code></td>
                    <td>
                        <span class="{{ $typeBadge }}">
                            {{ $coupon->discount_type === 'percent'
                  ? number_format($coupon->discount_value,2).' %'
                  : number_format($coupon->discount_value,2).' '.__('messages.currency') }}
                        </span>
                    </td>
                    <td>{{ $uses }}</td>
                    <td class="small">
                        {{ $coupon->starts_at?->format('Y-m-d') ?? '—' }} → {{ $coupon->ends_at?->format('Y-m-d') ?? '—' }}
                    </td>
                    <td><span class="{{ $statusBadge }}">{{ $statusLabel }}</span></td>
                    <td class="text-end">
                        <a href="{{ route('admin.coupons.edit', $coupon->id) }}" class="btn btn-sm btn-primary">
                            <i class="fa-regular fa-pen-to-square"></i>
                        </a>
                        <form action="{{ route('admin.coupons.destroy', $coupon->id) }}" method="POST" class="d-inline-block">
                            @csrf @method('DELETE')
                            <button onclick="return confirm('{{ __('messages.confirm_delete') }}')" class="btn btn-sm btn-danger">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">{{ __('messages.no_data') }}</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($coupons instanceof \Illuminate\Pagination\LengthAwarePaginator)
    <div class="card-body">
        {{ $coupons->appends(request()->query())->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>
@endsection