{{-- resources/views/Admin/pages/permissions/index.blade.php --}}
@extends('Admin.layout.app')
@section('title', __('messages.permissions'))

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    {{-- Header + Add --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0"><i class="bx bx-key me-2"></i>{{ __('messages.permissions') }}</h4>

        <a href="{{ route('admin.permissions.create') }}"
            class="btn btn-primary"
            title="{{ __('messages.add') }}"
            data-bs-toggle="tooltip" data-bs-placement="left">
            <i class="bi bi-plus-circle"></i>
        </a>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('messages.close') }}"></button>
    </div>
    @endif

    {{-- Search --}}
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.permissions.index') }}" id="searchForm">
                <div class="row g-2 align-items-end">
                    <div class="col-12 col-md-4">
                        <label class="form-label">{{ __('messages.search') }}</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bx bx-search"></i></span>
                            <input type="text" name="search" class="form-control"
                                placeholder="{{ __('messages.search') }}"
                                value="{{ request('search') }}" id="searchInput">
                        </div>
                    </div>

                    <div class="col-12 col-md-2">
                        <button class="btn btn-secondary w-100">
                            <i class="bx bx-filter-alt"></i> {{ __('messages.filter') }}
                        </button>
                    </div>

                    @if(request('search'))
                    <div class="col-12 col-md-2">
                        <a href="{{ route('admin.permissions.index') }}" class="btn btn-outline-secondary w-100">
                            {{ __('messages.clear') }}
                        </a>
                    </div>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- Table --}}
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width:80px">#</th>
                            <th>{{ __('messages.name') }}</th>
                            <th style="width:160px" class="text-end">{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($permissions as $permission)
                        <tr>
                            <td>{{ ($permissions->currentPage()-1)*$permissions->perPage() + $loop->iteration }}</td>
                            <td>
                                {{ __('messages.' . $permission->name, [], app()->getLocale()) !== 'messages.permissions.' . $permission->name
                      ? __('messages.' . $permission->name)
                      : str_replace('_', ' ', $permission->name) }}
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.permissions.edit', $permission->id) }}"
                                    class="btn btn-sm btn-primary" title="{{ __('messages.edit') }}"
                                    data-bs-toggle="tooltip"><i class="fa-regular fa-pen-to-square"></i></a>

                                <form action="{{ route('admin.permissions.destroy', $permission->id) }}"
                                    method="POST" class="d-inline-block"
                                    onsubmit="return confirm('{{ __('messages.confirm_delete') }}')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-danger" title="{{ __('messages.delete') }}" data-bs-toggle="tooltip">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted py-4">{{ __('messages.no_data_found') }}</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($permissions instanceof \Illuminate\Pagination\LengthAwarePaginator)
            <div class="p-3">
                {{ $permissions->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>
            @endif
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