{{-- resources/views/Admin/pages/roles/index.blade.php --}}
@extends('Admin.layout.app')
@section('roles_active', 'active')
@section('title', __('messages.roles'))

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4>{{ __('messages.roles') }}</h4>

    <div class="card">
        <div class="card-body">

            {{-- Top bar: Search (left) + Add (right) --}}
            <div class="d-flex justify-content-between align-items-center mb-3">
                <form method="GET" action="{{ route('admin.roles.index') }}" id="searchForm" class="d-flex" style="gap:10px;">
                    <input
                        type="text"
                        name="search"
                        id="searchInput"
                        class="form-control bg-light text-dark"
                        placeholder="{{ __('messages.search') }}"
                        value="{{ request('search') }}"
                        style="width:250px;">
                </form>

                <div>
                    <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> {{ __('messages.add') }}
                    </a>
                </div>
            </div>

            <div class="table-responsive text-nowrap">
                <table class="table table-striped text-black text-center">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('messages.name') }}</th>
                            <th>{{ __('messages.permissions') }}</th>
                            <th>{{ __('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($roles as $role)
                        <tr>
                            <td>{{ ($roles->currentPage()-1)*$roles->perPage() + $loop->iteration }}</td>
                            <td>{{ $role->name }}</td>
                            <td>
                                <span class="badge bg-label-primary text-primary">{{ $role->permissions->count() }}</span>
                            </td>
                            <td class="text-nowrap">
                                <a href="{{ route('admin.roles.edit', $role->id) }}" class="btn btn-sm btn-primary" title="{{ __('messages.edit') }}">
                                    <i class="fa-regular fa-pen-to-square"></i>
                                </a>
                                <form action="{{ route('admin.roles.destroy', $role->id) }}" method="POST" class="d-inline-block">
                                    @csrf @method('DELETE')
                                    <button onclick="return confirm('{{ __('messages.confirm_delete') }}')" class="btn btn-sm btn-danger" title="{{ __('messages.delete') }}">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center">{{ __('messages.no_data_found') }}</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    <div class="d-flex justify-content-center mt-3">
        {{ $roles->appends(request()->query())->links('pagination::bootstrap-4') }}
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const input = document.getElementById('searchInput');
        const form = document.getElementById('searchForm');
        let timer = null;

        if (input && form) {
            input.addEventListener('input', function() {
                clearTimeout(timer);
                timer = setTimeout(() => form.submit(), 500);
            });
        }
    });
</script>
@endpush