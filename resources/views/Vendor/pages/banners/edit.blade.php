@extends('Admin.layout.app')

@section('title', __('messages.edit_banner'))

@section('content')
<div class="card">
    <div class="card-body">
        <h5 class="card-title">{{ __('messages.edit_banner') }}</h5>

        <form method="POST" action="{{ route('banners.update', $banner->id) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- Dynamic Translations --}}
            @foreach (LaravelLocalization::getSupportedLocales() as $localeCode => $locale)
            <div class="mb-3">
                <label class="form-label">{{ __('messages.title') }} ({{ strtoupper($localeCode) }})</label>
                <input type="text" name="title[{{ $localeCode }}]" class="form-control @error(" title.$localeCode") is-invalid @enderror"
                    value="{{ old("title.$localeCode") ?? (json_decode($banner->getRawOriginal('title'), true)[$localeCode] ?? '') }}">
                @error("title.$localeCode")
                <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">{{ __('messages.description') }} ({{ strtoupper($localeCode) }})</label>
                <textarea name="description[{{ $localeCode }}]" class="form-control @error(" description.$localeCode") is-invalid @enderror">{{ old("description.$localeCode") ?? (json_decode($banner->getRawOriginal('description'), true)[$localeCode] ?? '') }}</textarea>
                @error("description.$localeCode")
                <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
            @endforeach

            {{-- Image --}}
            <div class="mb-3">
                <label class="form-label">{{ __('messages.image') }}</label>
                <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/*">
                @error('image')
                <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror

                {{-- Check if image exists and display it --}}
                @if($banner->image)
                <div class="mt-2">
                    <img src="{{ asset($banner->image) }}" width="80" alt="current image">
                    <small class="text-white d-block mt-1">{{ basename($banner->image) }}</small>
                </div>
                @endif
            </div>

            <button type="submit" class="btn btn-primary">{{ __('messages.update') }}</button>
            <a href="{{ route('banners.index') }}" class="btn btn-secondary">{{ __('messages.cancel') }}</a>
        </form>
    </div>
</div>
@endsection