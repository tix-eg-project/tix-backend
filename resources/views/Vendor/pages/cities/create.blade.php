@extends('Admin.layout.app')
@section('title', __('messages.Add City'))

@section('content')
    <div class="card">
        <div class="card-body">
            <h5 class="card-title text-center">{{ __('messages.Add City') }}</h5>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('cities.store') }}" enctype="multipart/form-data">
                @csrf

                {{-- Dynamic Translations --}}
                @foreach (LaravelLocalization::getSupportedLocales() as $localeCode => $locale)
                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.Name') }} ({{ strtoupper($localeCode) }})</label>
                        <input type="text" name="name[{{ $localeCode }}]"
                            class="form-control @error('name.' . $localeCode) is-invalid @enderror"
                            value="{{ old('name.' . $localeCode) }}">
                        @error('name.' . $localeCode)
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                @endforeach

                 <div class="form-group">
                    <label for="country_id">{{ __('messages.Select Country') }}</label>
                    <select name="country_id" class="form-control @error('country_id') is-invalid @enderror" required>
                        <option value="" disabled selected hidden>-- {{ __('messages.Select Country') }} --</option>
                        @foreach ($coutries as $country)
                            <option value="{{ $country->id }}" {{ old('country_id') == $country->id ? 'selected' : '' }}>
                                {{ $country->name ?? 'Country #' . $country->id }}
                            </option>
                        @endforeach
                    </select>
                    @error('country_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>


                <button type="submit" class="btn btn-primary">{{ __('messages.save') }}</button>
                <a href="{{ route('cities.index') }}" class="btn btn-secondary">{{ __('messages.cancel') }}</a>
            </form>
        </div>
    </div>
@endsection
