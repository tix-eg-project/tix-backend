@extends('Admin.layout.app')
@section('title', __('messages.add_banner'))

@section('content')
<div class="card">
    <div class="card-body">
        <h5 class="card-title">{{ __('messages.add_banner') }}</h5>

        <form method="POST" action="{{ route('banners.store') }}" enctype="multipart/form-data">
            @csrf

            {{-- Dynamic Translations --}}
            @foreach (LaravelLocalization::getSupportedLocales() as $localeCode => $locale)
            <div class="mb-3">
                <label class="form-label">{{ __('messages.title') }} ({{ strtoupper($localeCode) }})</label>
                <input type="text" name="title[{{ $localeCode }}]" class="form-control @error(" title.$localeCode") is-invalid @enderror" value="{{ old("title.$localeCode") }}">
                @error("title.$localeCode")
                <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">{{ __('messages.description') }} ({{ strtoupper($localeCode) }})</label>
                <textarea name="description[{{ $localeCode }}]" class="form-control @error(" description.$localeCode") is-invalid @enderror">{{ old("description.$localeCode") }}</textarea>
                @error("description.$localeCode")
                <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
            @endforeach

            <div class="mb-3">
                <label class="form-label">{{ __('messages.image') }}</label>
                <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/*">
                @error('image')
                <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">{{ __('messages.save') }}</button>
            <a href="{{ route('banners.index') }}" class="btn btn-secondary">{{ __('messages.cancel') }}</a>
        </form>
    </div>
</div>
@endsection