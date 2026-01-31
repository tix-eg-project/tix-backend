@extends('Admin.layout.app')

@section('contact_us_active', 'active')
@section('contact_us_open', 'open')

@section('title', __('messages.contact_us'))
@section('page_title', __('messages.contact_us'))

@section('content')
<div class="container-xxl container-p-y">

    {{-- Header --}}
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
        <h4 class="mb-0">{{ __('messages.contact_us') }}</h4>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('messages.close') }}"></button>
    </div>
    @endif

    {{-- Search only (no filters) --}}
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('contact_us.index') }}" id="searchForm">
                <div class="row g-2 align-items-center">
                    <div class="col-12 col-md-8">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bx bx-search"></i></span>
                            <input type="text"
                                name="search"
                                class="form-control"
                                placeholder="{{ __('messages.search') }}"
                                value="{{ request('search') }}">
                            @if(request('search'))
                            <a href="{{ route('contact_us.index', collect(request()->except(['search','page']))->filter()->all()) }}"
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
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover table-striped align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width:56px">#</th>
                        <th>{{ __('messages.full_name') }}</th>
                        <th>{{ __('messages.email') }}</th>
                        <th>{{ __('messages.phone') }}</th>
                        <th>{{ __('messages.subject') }}</th>
                        <th>{{ __('messages.message') }}</th>
                        <th class="text-end" style="width:100px">{{ __('messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($messages as $item)
                    <tr>
                        <td>{{ $messages->firstItem() ? $messages->firstItem() + $loop->index : $loop->iteration }}</td>
                        <td>{{ $item->full_name ?: '—' }}</td>
                        <td>{{ $item->email ?: '—' }}</td>
                        <td>{{ $item->phone ?: '—' }}</td>
                        <td>{{ $item->subject ?: '—' }}</td>
                        <td class="text-muted">
                            {{ \Illuminate\Support\Str::limit((string)($item->message ?? ''), 80) ?: '—' }}
                        </td>
                        <td class="text-end">
                            <form action="{{ route('contact_us.destroy', $item->id) }}" method="POST" class="d-inline-block">
                                @csrf @method('DELETE')
                                <button onclick="return confirm('{{ __('messages.confirm_delete') }}')"
                                    class="btn btn-sm btn-danger">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">{{ __('messages.no_data') }}</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($messages instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div class="card-body">
            {{ $messages->appends(request()->query())->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>

</div>
@endsection