@extends('Vendor.layout.app')
@section('offer_active', 'active')
@section('title', __('messages.Offers'))

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
            <h4 class="mb-0">{{ __('messages.Offers') }}</h4>
            <a href="{{ route('vendor.offers.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-1"></i> {{ __('messages.Add Offer+') }}
            </a>
        </div>

        {{-- Filters --}}
        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" action="{{ route('vendor.offers.index') }}" id="filterForm">
                    <div class="row g-2 align-items-end">
                        {{-- Search --}}
                        <div class="col-12 col-md-4">
                            <label class="form-label">{{ __('messages.Search by Offer name') }}</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bx bx-search"></i></span>
                                <input type="text"
                                    name="search"
                                    id="searchInput"
                                    class="form-control"
                                    placeholder="{{ __('messages.Search by Offer name') }}"
                                    value="{{ request('search') }}">
                                @if(request()->hasAny(['search','amount_type','status']) && collect(request()->only(['search','amount_type','status']))->filter()->isNotEmpty())
                                <a href="{{ route('vendor.offers.index') }}" class="btn btn-outline-secondary">
                                    {{ __('messages.clear') }}
                                </a>
                                @endif
                            </div>
                        </div>

                        {{-- Amount Type --}}
                        <div class="col-6 col-md-3">
                            <label class="form-label">{{ __('messages.Amount Type') }}</label>
                            <select name="amount_type" class="form-select" onchange="this.form.submit()">
                                <option value="">{{ __('messages.all') }}</option>
                                <option value="1" @selected(request('amount_type')==='1' )>{{ __('messages.percent') }}</option>
                                <option value="2" @selected(request('amount_type')==='2' )>{{ __('messages.fixed') }}</option>
                            </select>
                        </div>

                        {{-- Status --}}
                        <div class="col-6 col-md-3">
                            <label class="form-label">{{ __('messages.Status') }}</label>
                            <select name="status" class="form-select" onchange="this.form.submit()">
                                <option value="">{{ __('messages.all') }}</option>
                                <option value="active" @selected(request('status')==='active' )>{{ __('messages.active') }}</option>
                                <option value="inactive" @selected(request('status')==='inactive' )>{{ __('messages.inactive') }}</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Table --}}
        <div class="card">
            <div class="card-body">
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover table-striped text-center align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width:80px">{{ __('messages.Status') }}</th>
                                <th>{{ __('messages.Name') }}</th>
                                <th>{{ __('messages.Amount Type') }}</th>
                                <th>{{ __('messages.Amount Value') }}</th>
                                <th>{{ __('messages.Start Date') }}</th>
                                <th>{{ __('messages.End Date') }}</th>
                                <th>{{ __('messages.Products') }}</th>
                                <th style="width:140px">{{ __('messages.Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($offers as $offer)
                            @php
                            $amountTypeLabel = ((int)$offer->amount_type === 1)
                            ? __('messages.percent')
                            : __('messages.fixed');
                            @endphp
                            <tr>
                                <td>
                                    <div class="form-check form-switch d-inline-flex align-items-center justify-content-center">
                                        <input type="checkbox"
                                            class="form-check-input toggle-status"
                                            data-url="{{ url('vendor/offers/'.$offer->id.'/toggle-status') }}"
                                            {{ $offer->is_active ? 'checked' : '' }}>
                                    </div>
                                </td>
                                <td class="text-start">{{ $offer->name }}</td>
                                <td>{{ $amountTypeLabel }}</td>
                                <td>{{ number_format((float)$offer->amount_value, 2) }}</td>
                                <td>{{ $offer->start_date ? \Illuminate\Support\Carbon::parse($offer->start_date)->format('Y-m-d') : '—' }}</td>
                                <td>{{ $offer->end_date   ? \Illuminate\Support\Carbon::parse($offer->end_date)->format('Y-m-d')   : '—' }}</td>
                                <td class="text-start">
                                    @forelse ($offer->products as $product)
                                    <span class="badge bg-info-subtle text-info border border-info-subtle mb-1">{{ $product->name }}</span>
                                    @empty
                                    <span class="text-muted">—</span>
                                    @endforelse
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('vendor.offers.edit', $offer) }}" class="btn btn-sm btn-primary">
                                        <i class="fa-regular fa-pen-to-square"></i>
                                    </a>
                                    <form action="{{ route('vendor.offers.delete', $offer) }}" method="POST" class="d-inline-block">
                                        @csrf @method('DELETE')
                                        <button onclick="return confirm('{{ __('messages.confirm_delete') }}')" class="btn btn-sm btn-danger">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">{{ __('messages.no_data') }}</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($offers instanceof \Illuminate\Pagination\LengthAwarePaginator)
                <div class="pt-3">
                    {{ $offers->appends(request()->query())->links('pagination::bootstrap-4') }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Debounce search
        const input = document.getElementById('searchInput');
        const form = document.getElementById('filterForm');
        let t = null;
        if (input && form) {
            input.addEventListener('input', function() {
                clearTimeout(t);
                t = setTimeout(() => form.submit(), 500);
            });
        }

        // Toggle status via vendor route
        document.querySelectorAll('.toggle-status').forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                const url = this.dataset.url;
                const isActive = this.checked ? 1 : 0;
                const self = this;

                fetch(url, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            is_active: isActive
                        })
                    })
                    .then(r => r.ok ? r.json() : Promise.reject())
                    .then(data => {
                        if (data && data.message) {
                            // اختياري: Toast/Alert
                            // alert(data.message);
                        }
                    })
                    .catch(() => {
                        // رجّع الحالة القديمة لو فشل
                        self.checked = !self.checked;
                        alert('Action failed');
                    });
            });
        });
    });
</script>
@endpush