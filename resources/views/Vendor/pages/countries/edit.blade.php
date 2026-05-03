@extends('Admin.layout.app')

@section('title', __('messages.Update Country'))

@section('content')
    <div class="card">
        <div class="card-body">
            <h5 class="card-title text-center">{{ __('messages.Update Country') }}</h5>

            <form method="POST" action="{{ route('country.update', $country->id) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- Dynamic Translations --}}
                @foreach (LaravelLocalization::getSupportedLocales() as $localeCode => $locale)
                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.name') }} ({{ strtoupper($localeCode) }})</label>
                        <input type="text" name="name[{{ $localeCode }}]"
                            class="form-control @error(" name.$localeCode") is-invalid @enderror"
                            value="{{ old("name.$localeCode") ?? (json_decode($country->getRawOriginal('name'), true)[$localeCode] ?? '') }}">
                        @error("name.$localeCode")
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                @endforeach

                <button type="submit" class="btn btn-primary">{{ __('messages.update') }}</button>
                <a href="{{ route('country.index') }}" class="btn btn-secondary">{{ __('messages.cancel') }}</a>
            </form>
        </div>
    </div>
@endsection
