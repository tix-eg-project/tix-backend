@extends('Admin.layout.app')

@section('title', __('messages.Update City'))

@section('content')
    <div class="card">
        <div class="card-body">
            <h5 class="card-title text-center">{{ __('messages.Update City') }}</h5>

            <form method="POST" action="{{ route('cities.update', $city->id) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- Dynamic Translations --}}
                @foreach (LaravelLocalization::getSupportedLocales() as $localeCode => $locale)
                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.name') }} ({{ strtoupper($localeCode) }})</label>
                        <input type="text" name="name[{{ $localeCode }}]"
                            class="form-control @error(" name.$localeCode") is-invalid @enderror"
                            value="{{ old("name.$localeCode") ?? (json_decode($city->getRawOriginal('name'), true)[$localeCode] ?? '') }}">
                        @error("name.$localeCode")
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                @endforeach

                <div class="form-group">
                    <label for="country_id">{{ __('messages.Select Country') }}</label>
                    <select name="country_id" class="form-control @error('country_id') is-invalid @enderror" required>
                        <option value="" disabled hidden>-- {{ __('messages.Select Country') }} --</option>
                        @foreach ($countries as $country)
                            <option value="{{ $country->id }}"
                                {{ (old('country_id') ?? $city->country_id) == $country->id ? 'selected' : '' }}>
                                {{ $country->name }}
                            </option>
                        @endforeach
                    </select>

                    @error('country_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">{{ __('messages.update') }}</button>
                <a href="{{ route('cities.index') }}" class="btn btn-secondary">{{ __('messages.cancel') }}</a>
            </form>
        </div>
    </div>
@endsection
