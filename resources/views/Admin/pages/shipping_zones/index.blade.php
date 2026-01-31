@extends('Admin.layout.app')
@section('title', __('messages.shipping_zones'))
@section('page_title', __('messages.shipping_zones'))

@push('styles')
<style>
    .hero {
        border-radius: 16px;
        background: linear-gradient(135deg, #0ea5ea 0%, #6a5af9 100%);
        color: #fff;
        padding: 18px 20px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, .08)
    }

    .chip {
        display: inline-flex;
        align-items: center;
        gap: .45rem;
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
</style>
@endpush

@section('content')
<div class="container-xxl container-p-y">

    {{-- Hero --}}
    <div class="hero d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-1">{{ __('messages.shipping_zones') }}</h4>
            <div class="d-flex flex-wrap gap-2">
                <span class="chip"><i class="bi bi-geo-alt"></i> {{ $zones->total() }} {{ __('messages.records') }}</span>
                <span class="chip"><i class="bi bi-filter"></i> {{ __('messages.search') }}: {{ request('search','—') }}</span>
            </div>
        </div>
        <a href="{{ route('admin.shipping_zones.create') }}" class="btn btn-light text-dark">
            <i class="bx bx-plus me-1"></i> {{ __('messages.add_shipping_zone') }}
        </a>
    </div>

    {{-- Alert --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('messages.close') }}"></button>
    </div>
    @endif

    {{-- Search --}}
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.shipping_zones.index') }}" id="searchForm">
                <div class="row g-2 align-items-center">
                    <div class="col-12 col-md-8">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bx bx-search"></i></span>
                            <input type="text" name="search" id="searchInput" class="form-control"
                                placeholder="{{ __('messages.search') }}" value="{{ request('search') }}">
                            @if(request('search'))
                            <a href="{{ route('admin.shipping_zones.index', collect(request()->except(['search','page']))->filter()->all()) }}"
                                class="btn btn-outline-secondary">
                                {{ __('messages.clear') }}
                            </a>
                            @endif
                        </div>
                    </div>
                    <div class="col-12 col-md-4 text-end">
                        <button class="btn btn-secondary w-100 w-md-auto">
                            <i class="bx bx-search"></i> {{ __('messages.search') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Table --}}
    <div class="card-clean p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width:56px">#</th>
                        @php
                        $loc = app()->getLocale();
                        $nameVal = $zone->name ?? '—';
                        if (is_array($nameVal)) { $nameVal = $nameVal[$loc] ?? reset($nameVal); }
                        @endphp
                        <td>{{ $nameVal }}</td>

                        <th>{{ __('messages.price') }}</th>
                        <th class="text-end" style="width:140px">{{ __('messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($zones as $zone)
                    @php
                    $loc = app()->getLocale();
                    $name = $zone->name_text
                    ?? (method_exists($zone,'getTranslation') ? $zone->getTranslation('name',$loc,true) : null)
                    ?? (is_array($zone->name ?? null) ? ($zone->name[$loc] ?? reset($zone->name)) : ($zone->name ?? '—'));
                    @endphp
                    <tr>
                        <td>{{ $zones->firstItem() ? $zones->firstItem() + $loop->index : $loop->iteration }}</td>
                        <td>{{ $name }}</td>
                        <td>{{ number_format((float)$zone->price, 2) }} {{ __('messages.currency') }}</td>
                        <td class="text-end">
                            <a href="{{ route('admin.shipping_zones.edit', $zone->id) }}" class="btn btn-sm btn-primary" title="{{ __('messages.edit') }}">
                                <i class="fa-regular fa-pen-to-square"></i>
                            </a>
                            <form action="{{ route('admin.shipping_zones.destroy', $zone->id) }}" method="POST" class="d-inline-block">
                                @csrf @method('DELETE')
                                <button onclick="return confirm('{{ __('messages.confirm_delete') }}')" class="btn btn-sm btn-danger" title="{{ __('messages.delete') }}">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">{{ __('messages.no_data_found') }}</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($zones instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div class="p-3">
            {{ $zones->appends(request()->query())->links('pagination::bootstrap-5') }}
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
        if (form && input) {
            input.addEventListener('input', function() {
                clearTimeout(t);
                t = setTimeout(() => form.submit(), 500);
            });
        }
    });
</script>
@endpush