@extends('Admin.layout.app')

@section('title', __('messages.edit_category'))

@section('content')
<div class="card">
    <div class="card-body">
        <h5 class="card-title">{{ __('messages.edit_category') }}</h5>

        @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        <form method="POST" action="{{ route('categories.update', $category->id) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- Dynamic Translations --}}
            @foreach (LaravelLocalization::getSupportedLocales() as $localeCode => $locale)
            <div class="mb-3">
                <label class="form-label">{{ __('messages.name') }} ({{ strtoupper($localeCode) }})</label>
                <input type="text" name="name[{{ $localeCode }}]" class="form-control @error(" name.$localeCode") is-invalid @enderror"
                    value="{{ old("name.$localeCode") ?? (json_decode($category->getRawOriginal('name'), true)[$localeCode] ?? '') }}">
                @error("name.$localeCode")
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
                @if($category->image)
                <div class="mt-2">
                    <img src="{{ asset($category->image) }}" width="80" alt="current image">
                    <small class="text-white d-block mt-1">{{ basename($category->image) }}</small>
                </div>
                @endif
            </div>

            <button type="submit" class="btn btn-primary">{{ __('messages.update') }}</button>
            <a href="{{ route('categories.index') }}" class="btn btn-secondary">{{ __('messages.cancel') }}</a>
        </form>
    </div>
</div>
@endsection