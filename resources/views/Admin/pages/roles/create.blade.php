{{-- resources/views/Admin/pages/roles/create.blade.php --}}
@extends('Admin.layout.app')
@section('title', __('messages.add_role'))

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">
            <i class="bx bx-lock-alt me-2"></i>{{ __('messages.add_role') }}
        </h4>
        <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary" title="{{ __('messages.back') }}">
            <i class="bi bi-arrow-left"></i>
        </a>
    </div>

    <form method="POST" action="{{ route('admin.roles.store') }}">
        @csrf

        {{-- Role name --}}
        <div class="card mb-3">
            <div class="card-body">
                <h6 class="mb-2">
                    <i class="fas fa-user-tag me-2 text-primary"></i>{{ __('messages.name') }}
                </h6>
                <input type="text" name="name" class="form-control"
                    placeholder="{{ __('messages.enter_role_name') }}"
                    value="{{ old('name') }}">
                @error('name')
                <div class="alert alert-danger mt-2 mb-0">{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- Permissions --}}
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                    <h6 class="mb-0">
                        <i class="fas fa-shield-alt me-2 text-primary"></i>{{ __('messages.permissions') }}
                    </h6>
                    <div class="form-check">
                        <input type="checkbox" id="checkAll" class="form-check-input">
                        <label for="checkAll" class="form-check-label fw-semibold">{{ __('messages.select_all') }}</label>
                    </div>
                </div>

                <div class="accordion" id="permissionsAccordion">
                    @php
                    use Illuminate\Support\Str;
                    $groupedPermissions = $permissions->groupBy(fn($p) => Str::before($p->name, '_'));
                    @endphp

                    @foreach ($groupedPermissions as $group => $perms)
                    <div class="accordion-item border-0 mb-2 shadow-sm">
                        <h2 class="accordion-header" id="heading-{{ $group }}">
                            <button class="accordion-button bg-light text-dark fw-bold shadow-none" type="button"
                                data-bs-toggle="collapse" data-bs-target="#collapse-{{ $group }}" aria-expanded="true">
                                <i class="fas fa-folder me-2 text-primary"></i>
                                {{ __('messages.' . $group, [], app()->getLocale()) !== 'messages.' . $group
                      ? __('messages.' . $group)
                      : ucfirst(str_replace('_',' ',$group)) }}
                            </button>
                        </h2>
                        <div id="collapse-{{ $group }}" class="accordion-collapse collapse show" data-bs-parent="#permissionsAccordion">
                            <div class="accordion-body">
                                <div class="row">
                                    @foreach ($perms as $permission)
                                    <div class="col-md-4 col-sm-6 mb-2">
                                        <div class="form-check bg-white p-2 rounded border">
                                            <input type="checkbox" class="form-check-input perm-checkbox"
                                                name="permissions[]" value="{{ $permission->id }}"
                                                id="perm_{{ $permission->id }}">
                                            <label class="form-check-label ms-2" for="perm_{{ $permission->id }}">
                                                {{ __('messages.' . $permission->name, [], app()->getLocale()) !== 'messages.permissions.' . $permission->name
                                ? __('messages.' . $permission->name)
                                : str_replace('_',' ',$permission->name) }}
                                            </label>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

            </div>
        </div>

        {{-- Save --}}
        <div class="d-flex justify-content-end gap-2 mt-3">
            <button type="submit" class="btn btn-primary" title="{{ __('messages.save') }}">
                <i class="fas fa-save"></i>
            </button>
            <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary" title="{{ __('messages.cancel') }}">
                <i class="bi bi-x-lg"></i>
            </a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const checkAll = document.getElementById('checkAll');
        const boxes = document.querySelectorAll('.perm-checkbox');
        const syncAll = () => checkAll.checked = [...boxes].length > 0 && [...boxes].every(i => i.checked);
        if (checkAll) {
            checkAll.addEventListener('change', () => boxes.forEach(cb => cb.checked = checkAll.checked));
            boxes.forEach(cb => cb.addEventListener('change', syncAll));
            syncAll();
        }
    });
</script>
@endpush