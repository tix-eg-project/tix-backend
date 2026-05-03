@extends('Admin.layouts.app')

@section('title', __('messages.edit_profile'))

@section('content')
    <style>
        .profile-card {
            background-color: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .profile-image-preview {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #dee2e6;
        }

        .form-section-title {
            font-weight: bold;
            margin-bottom: 15px;
        }

        .form-control {
            background-color: #fff;
            color: #000 !important;
        }

        input[type="file"] {
            padding: 8px;
        }
    </style>

    <div class="container-fluid">
        <div class="profile-card">
            <h4 class="mb-4">@lang('messages.edit_profile')</h4>

            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.admins.profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row align-items-center mb-4">
                    <div class="col-md-3 text-center mb-3 mb-md-0">
                        @if ($admin->image_url)
                            <img src="{{ $admin->image_url }}" alt="Profile Image" class="profile-image-preview">
                        @else
                            <img src="{{ asset('template/images/icons/user.png') }}" alt="Default Image"
                                class="profile-image-preview">
                        @endif
                    </div>
                    <div class="col-md-9">
                        <label for="image" class="form-label">@lang('messages.image')</label>
                        <input type="file" name="image" class="form-control">
                        @error('image')
                            <small class="text-danger d-block mt-1">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="name">@lang('messages.name')</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $admin->name) }}">
                    @error('name')
                        <small class="text-danger d-block mt-1">{{ $message }}</small>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password">@lang('messages.password')</label>
                    <input type="password" name="password" class="form-control">
                    <small class="text-muted">@lang('messages.leave_blank_if_no_change')</small>
                    @error('password')
                        <small class="text-danger d-block mt-1">{{ $message }}</small>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i> @lang('messages.update')
                </button>
            </form>
        </div>
    </div>
@endsection
