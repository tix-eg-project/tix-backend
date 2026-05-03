@extends('Admin.layout.app')
@section('users_active', 'active')
@section('title', __('Add User'))
<style>
    select.form-control {
        color: #000 !important;
        background-color: #fff;
    }

    select.form-control option {
        color: #000 !important;
        background-color: #fff;
    }
</style>
@section('content')
    <!--start main wrapper-->
    {{-- @can('users-create') --}}
    <main class="main-wrapper">
        <div class="main-content">
            <div class="row mb-5">
                <div class="card-header bg-primary text-white rounded-top mb-4">
                    <h5 class="mb-0">{{ __('messages.Add a new user') }}</h5>
                </div>
                <div class="col-12">
                    <form method="post" action="{{ route('admin.pages.users.store') }}" class="p-4 rounded shadow-lg bg-white"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="card border-0">
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6 mb-4">
                                        <label for="name" class="form-label">{{ __('messages.Name') }}</label>
                                        <input type="text" name="name" value="{{ old('name') }}" id="name"
                                            class="form-control" placeholder="{{ __('messages.Enter the user name') }}">
                                        @error('name')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-4">
                                        <label for="email" class="form-label">{{ __('messages.Email') }}</label>
                                        <input type="email" name="email" value="{{ old('email') }}" id="email"
                                            class="form-control" placeholder="{{ __('messages.Enter the user email') }}">
                                        @error('email')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-4">
                                        <label for="image" class="form-label">{{ __('messages.Image') }}</label>
                                        <input type="file" value="{{ old('image') }}" name="image" id="image"
                                            class="form-control" placeholder="{{ __('Enter the user image') }}">
                                        @error('image')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>


                                    <div class="col-md-6 mb-4">
                                        <label for="phone" class="form-label">{{ __('messages.Mobile Number') }}</label>
                                        <input type="number" value="{{ old('phone') }}" name="phone" id="phone"
                                            class="form-control" placeholder="{{ __('messages.Enter the user mobile number') }}">
                                        @error('phone')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>


                                    <!--<div class="col-md-6">-->
                                    <!--    <label for="national_number" class="form-label">{{ __('National Number') }}</label>-->
                                    <!--    <input type="number" name="national_number" id="national_number" class="form-control" placeholder="{{ __('Enter the user national number') }}">-->
                                    <!--</div>-->
                                    <!--@error('national_number')
        -->
                                        <!--        <small class="text-danger">{{ $message }}</small>-->

                                        <!--
                                        @enderror-->

                                    <div class="col-md-6 mb-4">
                                        <label for="password" class="form-label">{{ __('messages.Password') }}</label>
                                        <input type="password" name="password" id="password" class="form-control"
                                            placeholder="{{ __('Enter the user password') }}">
                                        @error('password')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-4">
                                        <label for="role_id" class="form-label text-dark">{{ __('messages.Role') }}</label>
                                        <select class="form-select bg-white text-dark" value="{{ old('role_id') }}"
                                            name="role_id" id="role_id">
                                            <option value="" selected>{{ __('messages.Choose the user role') }}</option>
                                            @foreach ($roles as $role)
                                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('role_id')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                </div>
                                <div class="bg-light w-100">
                                    <button type="submit" class="btn btn-primary w-100 px-4">{{ __('messages.Save') }}</button>
                                </div>
                            </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
    {{-- @endcan --}}
    <!--end main wrapper-->
@endsection
