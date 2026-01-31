@extends('Admin.layout.app')

@section('notification_active', 'active')
@section('title', __('messages.Notifications'))
@section('page_title', __('messages.Notifications'))

@section('content')
<div class="container-xxl container-p-y">

    {{-- Header --}}
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
        <h4 class="mb-0">{{ __('messages.Notifications') }}</h4>
        <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
            {{ $notifications->total() }} {{ __('messages.records') }}
        </span>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('messages.close') }}"></button>
    </div>
    @endif

    {{-- Search (اختياري) --}}
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('Admin.notifications.index') }}" id="searchForm">
                <div class="row g-2 align-items-center">
                    <div class="col-12 col-md-8">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bx bx-search"></i></span>
                            <input type="text"
                                id="searchInput"
                                name="search"
                                class="form-control"
                                placeholder="{{ __('messages.search') }}"
                                value="{{ request('search') }}">
                            @if(request('search'))
                            <a href="{{ route('Admin.notifications.index', collect(request()->except(['search','page']))->filter()->all()) }}"
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
                        <th>{{ __('messages.Message') }}</th>
                        <th>{{ __('messages.Created_at') }}</th>
                        <th class="text-end" style="width:100px">{{ __('messages.Action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($notifications as $notification)
                    @php
                    $data = is_array($notification->data) ? $notification->data : (json_decode($notification->data, true) ?: []);
                    $msg = $data['message'] ?? $data['title'] ?? $data['body'] ?? '—';
                    @endphp
                    <tr>
                        <td>{{ $notifications->firstItem() ? $notifications->firstItem() + $loop->index : $loop->iteration }}</td>
                        <td class="text-start">{{ \Illuminate\Support\Str::limit($msg, 140) }}</td>
                        <td class="text-muted">{{ $notification->created_at?->diffForHumans() ?? '—' }}</td>
                        <td class="text-end">
                            <form action="{{ route('Admin.notifications.delete', $notification->id) }}"
                                method="POST" class="d-inline-block">
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
                        <td colspan="4" class="text-center py-4 text-muted">{{ __('messages.no_data') }}</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($notifications instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div class="card-body">
            {{ $notifications->appends(request()->query())->links('pagination::bootstrap-5') }}
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