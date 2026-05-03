{{-- resources/views/Admin/pages/permissions/create.blade.php --}}
@extends('Admin.layout.app')
@section('title', __('messages.add_permission'))

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    {{-- Header + Back --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0"><i class="bx bx-key me-2"></i>{{ __('messages.add_permission') }}</h4>
        <a href="{{ route('admin.permissions.index') }}" class="btn btn-outline-secondary"
            title="{{ __('messages.back') }}" data-bs-toggle="tooltip">
            <i class="bi bi-arrow-left"></i>
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.permissions.store') }}">
                @csrf

                <div class="mb-3">
                    <label for="name" class="form-label">{{ __('messages.name') }}</label>
                    <input type="text" id="name"
                        class="form-control @error('name') is-invalid @enderror"
                        name="name" value="{{ old('name') }}"
                        placeholder="{{ __('messages.enter_permission_name') ?? __('messages.name') }}">
                    @error('name')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <input type="hidden" name="guard_name" value="admin">

                <div class="d-flex justify-content-end gap-2">
                    <button type="submit" class="btn btn-primary" title="{{ __('messages.save') }}">
                        <i class="fas fa-save"></i>
                    </button>
                    <a href="{{ route('admin.permissions.index') }}" class="btn btn-outline-secondary" title="{{ __('messages.cancel') }}">
                        <i class="bi bi-x-lg"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection