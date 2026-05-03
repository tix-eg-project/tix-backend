@extends('Admin.layout.app')
@section('offer_active', 'active')
@section('title', __('messages.Offers'))
@section('page_title', __('messages.Offers'))

@section('content')
<div class="container-xxl container-p-y">

    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
        <h4 class="mb-0">{{ __('messages.Offers') }}</h4>
        <a href="{{ route('offer.create') }}" class="btn btn-primary">
            <i class="bx bx-plus"></i> {{ __('messages.Add Offer+') }}
        </a>
    </div>

    {{-- Search + Filters --}}
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('offer.index') }}" id="searchForm">
                <div class="row g-2 align-items-end">

                    {{-- search --}}
                    <div class="col-12 col-md-4">
                        <label class="form-label">{{ __('messages.Search by Offer name') }}</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bx bx-search"></i></span>
                            <input type="text" name="search" id="searchInput" class="form-control"
                                placeholder="{{ __('messages.Search by Offer name') }}"
                                value="{{ request('search') }}">
                            @if(request('search'))
                            <a href="{{ route('offer.index', collect(request()->except(['search','page']))->filter()->all()) }}"
                                class="btn btn-outline-secondary">{{ __('messages.clear') }}</a>
                            @endif
                        </div>
                    </div>

                    {{-- vendor --}}
                    <div class="col-6 col-md-2">
                        <label class="form-label">{{ __('messages.vendor') }}</label>
                        <select name="vendor_id" class="form-select" onchange="this.form.submit()">
                            <option value="">{{ __('messages.all') }}</option>
                            @foreach(($vendors ?? []) as $v)
                            <option value="{{ $v->id }}" @selected((string)request('vendor_id')===(string)$v->id)>
                                {{ $v->name_text ?? $v->name ?? ('#'.$v->id) }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- amount type --}}
                    <div class="col-6 col-md-2">
                        <label class="form-label">{{ __('messages.Amount Type') }}</label>
                        <select name="amount_type" class="form-select" onchange="this.form.submit()">
                            <option value="">{{ __('messages.all') }}</option>
                            <option value="1" @selected(request('amount_type')==='1' )>{{ __('messages.percent') }}</option>
                            <option value="2" @selected(request('amount_type')==='2' )>{{ __('messages.fixed') }}</option>
                        </select>
                    </div>

                    {{-- status --}}
                    <div class="col-6 col-md-2">
                        <label class="form-label">{{ __('messages.Status') }}</label>
                        <select name="is_active" class="form-select" onchange="this.form.submit()">
                            <option value="">{{ __('messages.all') }}</option>
                            <option value="1" @selected(request('is_active')==='1' )>{{ __('messages.active') }}</option>
                            <option value="0" @selected(request('is_active')==='0' )>{{ __('messages.inactive') }}</option>
                        </select>
                    </div>

                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="table-responsive text-nowrap">
            <table class="table table-hover table-striped text-center align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width:72px">{{ __('messages.Status') }}</th>
                        <th>{{ __('messages.Name') }}</th>
                        <th>{{ __('messages.Amount Type') }}</th>
                        <th>{{ __('messages.Amount Value') }}</th>
                        <th>{{ __('messages.Start Date') }}</th>
                        <th>{{ __('messages.End Date') }}</th>
                        <th>{{ __('messages.Vendor') }}</th>
                        <th>{{ __('messages.Products') }}</th>
                        <th class="text-end" style="width:140px">{{ __('messages.Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($offers as $offer)
                    @php
                    $name = $offer->name_text ?? $offer->name ?? ('#'.$offer->id);
                    $productsBadges = $offer->products ?? collect();
                    @endphp
                    <tr>
                        {{-- toggle --}}
                        <td>
                            <div class="form-check form-switch d-inline-block">
                                <input class="form-check-input toggle-status" type="checkbox"
                                    data-id="{{ $offer->id }}" {{ $offer->is_active ? 'checked' : '' }}>
                            </div>
                        </td>

                        <td class="fw-semibold">{{ $name }}</td>

                        <td>
                            @if((int)$offer->amount_type === 1)
                            <span class="badge bg-info">{{ __('messages.percent') }}</span>
                            @elseif((int)$offer->amount_type === 2)
                            <span class="badge bg-primary">{{ __('messages.fixed') }}</span>
                            @else
                            <span class="badge bg-secondary">{{ $offer->amount_type }}</span>
                            @endif
                        </td>

                        <td>{{ rtrim(rtrim(number_format((float)$offer->amount_value, 2), '0'), '.') }}</td>
                        <td>{{ $offer->start_date ? \Illuminate\Support\Carbon::parse($offer->start_date)->format('Y-m-d') : '—' }}</td>
                        <td>{{ $offer->end_date   ? \Illuminate\Support\Carbon::parse($offer->end_date)->format('Y-m-d')   : '—' }}</td>

                        <td>
                            @if($offer->vendor_id)
                            <span class="badge bg-warning text-dark">{{ optional($offer->vendor)->name ?? ('#'.$offer->vendor_id) }}</span>
                            @else
                            <span class="badge bg-secondary">{{ __('messages.Admin') }}</span>
                            @endif
                        </td>

                        <td class="text-start">
                            @if($productsBadges->count())
                            @php $max=4; @endphp
                            @foreach($productsBadges->take($max) as $p)
                            <span class="badge bg-info me-1 mb-1">{{ $p->name ?? ('#'.$p->id) }}</span>
                            @endforeach
                            @if($productsBadges->count() > $max)
                            <span class="badge bg-light text-dark border">+{{ $productsBadges->count()-$max }}</span>
                            @endif
                            @else
                            <span class="text-muted">—</span>
                            @endif
                        </td>

                        <td class="text-end">
                            <a href="{{ route('offer.edit', $offer) }}" class="btn btn-sm btn-primary">
                                <i class="fa-regular fa-pen-to-square"></i>
                            </a>
                            <form action="{{ route('offer.delete', $offer) }}" method="POST" class="d-inline-block">
                                @csrf @method('DELETE')
                                <button onclick="return confirm('{{ __('messages.confirm_delete') }}')" class="btn btn-sm btn-danger">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-4 text-muted">{{ __('messages.no_data') }}</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($offers instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div class="card-body">
            {{ $offers->withQueryString()->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('searchForm');
        const input = document.getElementById('searchInput');
        let t = null;
        if (input && form) {
            input.addEventListener('input', function() {
                clearTimeout(t);
                t = setTimeout(() => form.submit(), 500);
            });
        }

        document.querySelectorAll('.toggle-status').forEach(function(ch) {
            ch.addEventListener('change', function() {
                const offerId = this.dataset.id;
                const isActive = this.checked ? 1 : 0;

                fetch(`/admin/offer/${offerId}/toggle-status`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        is_active: isActive
                    })
                }).then(r => r.json()).then(d => {
                    if (d?.message) {
                        // لو حابب Toast بدل alert قولّي
                        console.log(d.message);
                    }
                }).catch(console.error);
            });
        });
    });
</script>
@endpush