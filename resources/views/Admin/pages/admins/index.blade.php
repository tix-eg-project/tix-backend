@extends('Admin.layout.app')
@section('title', __('messages.admins'))

@push('styles')
<style>
    .page-pad {
        padding: 20px;
        padding-top: 28px;
    }

    @media (min-width:1200px) {
        .page-pad {
            padding-left: 28px;
            padding-right: 28px;
        }
    }

    .card-clean {
        border: 1px solid rgba(0, 0, 0, .08);
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, .06);
        background: #fff;
    }

    .table thead th {
        background: #f7f7fb !important;
        border: 0 !important;
        font-weight: 700;
        color: #424750;
    }

    .btn-icon {
        width: 34px;
        height: 34px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
    }
</style>
@endpush

@section('content')
<div class="page-pad">

    {{-- Header + Search + Add --}}
    <div class="card-clean p-3 mb-3">
        <div class="d-flex justify-content-between align-items-center gap-2 flex-wrap">
            <h4 class="mb-0">
                <i class="bx bx-user-circle me-2"></i>{{ __('messages.admins') }}
            </h4>
            <form method="GET" action="{{ route('admin.admins.index') }}" id="searchForm" class="d-flex" style="gap:10px;">
                <input type="text" name="search" id="searchInput" class="form-control bg-light text-dark"
                    placeholder="{{ __('messages.search') }}" value="{{ request('search') }}" style="width:260px;">
            </form>
            <a href="{{ route('admin.admins.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> {{ __('messages.create_new') }}
            </a>
        </div>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- Table --}}
    <div class="card-clean p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width:80px">#</th>
                        <th>{{ __('messages.name') }}</th>
                        <th>{{ __('messages.email') }}</th>
                        <th>{{ __('messages.role') }}</th>
                        <th class="text-end" style="width:160px">{{ __('messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($admins as $admin)
                    <tr>
                        <td>{{ ($admins->currentPage()-1)*$admins->perPage()+$loop->iteration }}</td>
                        <td class="fw-semibold">{{ $admin->name }}</td>
                        <td>{{ $admin->email }}</td>
                        <td>{{ optional($admin->roles->first())->name ?? '—' }}</td>
                        <td class="text-end">
                            <a href="{{ route('admin.admins.edit', $admin->id) }}"
                                class="btn btn-sm btn-primary btn-icon" title="{{ __('messages.edit') }}">
                                <i class="fa-regular fa-pen-to-square"></i>
                            </a>

                            @if(auth('admin')->id() !== $admin->id)
                            <form action="{{ route('admin.admins.destroy', $admin->id) }}"
                                method="POST" class="d-inline-block"
                                onsubmit="return confirm('{{ __('messages.confirm_delete') }}')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger btn-icon" title="{{ __('messages.delete') }}">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">{{ __('messages.no_data_found') }}</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-3">
            {{ $admins->appends(request()->query())->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const input = document.getElementById('searchInput');
        const form = document.getElementById('searchForm');
        let t = null;
        if (input && form) {
            input.addEventListener('input', function() {
                clearTimeout(t);
                t = setTimeout(() => form.submit(), 500);
            });
        }
    });
</script>
@endpush