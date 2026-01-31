@extends('Admin.layouts.app')
@section('title', __('Edit User'))
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
    <main class="main-wrapper">
        <div class="main-content">
            <div class="row mb-5">
                <div class="col-12 col-xl-10 offset-xl-1">
                    <form method="post" action="{{ route('admin.pages.users.update', $user) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="card shadow-sm rounded-4">
                            <div class="card-header bg-primary text-white rounded-top-4">
                                <h5 class="mb-0">{{ __('Modify User Data') }}</h5>
                            </div>

                            <div class="card-body">
                                <div class="row g-4">

                                    <!-- Name -->
                                    <div class="col-md-6">
                                        <label for="name" class="form-label">{{ __('menu.Name') }}</label>
                                        <input type="text" name="name" class="form-control"
                                            placeholder="{{ __('Enter the user name') }}"
                                            value="{{ old('name', $user->name) }}">
                                        @error('name')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Email -->
                                    <div class="col-md-6">
                                        <label for="email" class="form-label">{{ __('menu.Email') }}</label>
                                        <input type="email" name="email" id="email" class="form-control"
                                            placeholder="{{ __('Enter the user email') }}"
                                            value="{{ old('email', $user->email) }}">
                                        @error('email')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Image -->
                                    <div class="col-md-6">
                                        <label for="image" class="form-label">{{ __('menu.Image') }}</label>
                                        <input type="file" name="image" value="{{ old('image') }}" id="image"
                                            class="form-control">
                                        @error('image')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                        @if ($user->image)
                                            <div class="mt-2">
                                                <img src="{{ asset($user->image) }}" alt="{{ $user->name }}"
                                                    class="rounded shadow" width="100">
                                            </div>
                                        @endif

                                    </div>

                                    <!-- Phone -->
                                    <div class="col-md-6">
                                        <label for="phone" class="form-label">{{ __('menu.Mobile Number') }}</label>
                                        <input type="number" name="phone" id="phone" class="form-control"
                                            placeholder="{{ __('Enter the user mobile number') }}"
                                            value="{{ old('phone', $user->phone) }}">
                                        @error('phone')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>


                                    <div class="form-group">
                                        <label for="country_id">{{ __('menu.Select Country') }}</label>
                                        <select name="country_id"
                                            class="form-control @error('country_id') is-invalid @enderror" required>
                                            <option value="" disabled hidden>{{ __('menu.Select Country') }}</option>
                                            @foreach ($countries as $country)
                                                <option value="{{ $country->id }}"
                                                    {{ old('country_id', $user->country_id) == $country->id ? 'selected' : '' }}>
                                                    {{ $country->name ?? 'Country #' . $country->id }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('country_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="city_id">{{ __('menu.Select City') }}</label>
                                        <select name="city_id" class="form-control @error('city_id') is-invalid @enderror"
                                            required>
                                            <option value="" disabled hidden>{{ __('menu.Select City') }}</option>
                                            @foreach ($cities as $city)
                                                <option value="{{ $city->id }}"
                                                    {{ old('city_id', $user->city_id) == $city->id ? 'selected' : '' }}>
                                                    {{ $city->name ?? 'City #' . $city->id }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('city_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- National Number -->
                                    <!--<div class="col-md-6">-->
                                    <!--    <label for="national_number" class="form-label">{{ __('National Number') }}</label>-->
                                    <!--    <input type="number" name="national_number" id="national_number" class="form-control" placeholder="{{ __('Enter the user national number') }}" value="{{ old('national_number', $user->national_number) }}">-->
                                    <!--    @error('national_number')
        <div class="text-danger small mt-1">{{ $message }}</div>
    @enderror-->
                                    <!--</div>-->

                                    <!-- Password -->
                                    <div class="col-md-6">
                                        <label for="password" class="form-label">{{ __('menu.Password') }}</label>
                                        <input type="password" value="{{ old('password', $user->password) }}"
                                            name="password" id="password" class="form-control"
                                            placeholder="{{ __('menu.Enter the user password') }}">
                                        @error('password')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Role -->
                                    <div class="col-md-6">
                                        <label for="role_id" class="form-label">{{ __('menu.Role') }}</label>
                                        <select class="form-select form-control @error('role_id') is-invalid @enderror"
                                            name="role_id" id="role_id">
                                            <option value="">{{ __('menu.Choose the user role') }}</option>
                                            @foreach ($roles as $role)
                                                <option value="{{ $role->id }}"
                                                    {{ old('role_id', optional($user->roles->first())->id) == $role->id ? 'selected' : '' }}>
                                                    {{ $role->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('role_id')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>


                                </div>
                            </div>

                            <div class="card-footer text-end">
                                <button type="submit" class="btn btn-success px-4">{{ __('menu.Update') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
@endsection
