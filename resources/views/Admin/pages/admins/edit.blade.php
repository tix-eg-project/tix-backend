@extends('Admin.layout.app')
@section('title', __('messages.edit'))

@push('styles')
<style>
    .page-wrap {
        padding: 24px;
    }

    @media (min-width:1200px) {
        .page-wrap {
            padding-left: 28px;
            padding-right: 28px;
        }
    }

    .card-clean {
        border: 1px solid rgba(0, 0, 0, .08);
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, .06);
        background: #fff;
        overflow: hidden;
    }

    .card-clean .card-head {
        padding: 16px 18px;
        border-bottom: 1px solid #f0f1f3;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        background: #fff;
    }

    .card-clean .card-body {
        padding: 20px;
    }
</style>
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y page-wrap">

    <div class="card-clean">
        <div class="card-head">
            <h4 class="mb-0"><i class="bx bx-edit-alt me-2"></i>{{ __('messages.edit') }}</h4>
            <a href="{{ route('admin.admins.index') }}" class="btn btn-light border">
                <i class="bi bi-arrow-left"></i> {{ __('messages.back') }}
            </a>
        </div>

        <div class="card-body">
            @if($errors->any())
            <div class="alert alert-danger mb-3">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form action="{{ route('admin.admins.update', $admin->id) }}" method="POST">
                @csrf @method('PUT')

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">{{ __('messages.name') }}</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $admin->name) }}">
                        @error('name') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">{{ __('messages.email') }}</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $admin->email) }}">
                        @error('email') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">{{ __('messages.password') }}</label>
                        <input type="password" name="password" class="form-control">
                        <small class="text-muted">{{ __('messages.leave_blank_if_no_change') }}</small>
                        @error('password') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">{{ __('messages.role') }}</label>
                        <select name="role" class="form-select">
                            @foreach($roles as $id => $name)
                            <option value="{{ $id }}" {{ $admin->roles->first()?->id == $id ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                            @endforeach
                        </select>
                        @error('role') <small class="text-danger d-block mt-1">{{ $message }}</small> @enderror
                    </div>
                </div>

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>{{ __('messages.update') }}
                    </button>
                    <a href="{{ route('admin.admins.index') }}" class="btn btn-outline-secondary">
                        {{ __('messages.cancel') }}
                    </a>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection