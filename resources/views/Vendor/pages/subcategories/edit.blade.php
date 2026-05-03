@extends('Admin.layout.app')

@section('title', __('messages.edit_subcategory'))

@section('content')
<div class="card">
    <div class="card-body">
        <h5 class="card-title">{{ __('messages.edit_subcategory') }}</h5>

        <form method="POST" action="{{ route('subcategories.update', $subcategory->id) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- Dynamic Translations --}}
            @foreach (LaravelLocalization::getSupportedLocales() as $localeCode => $locale)
            <div class="mb-3">
                <label class="form-label">{{ __('messages.name') }} ({{ strtoupper($localeCode) }})</label>
                <input type="text" name="name[{{ $localeCode }}]" class="form-control @error(" name.$localeCode") is-invalid @enderror"
                    value="{{ old("name.$localeCode") ?? (json_decode($subcategory->getRawOriginal('name'), true)[$localeCode] ?? '') }}">
                @error("name.$localeCode")
                <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">{{ __('messages.description') }} ({{ strtoupper($localeCode) }})</label>
                <textarea name="description[{{ $localeCode }}]" class="form-control @error(" description.$localeCode") is-invalid @enderror">{{ old("description.$localeCode") ?? (json_decode($subcategory->getRawOriginal('description'), true)[$localeCode] ?? '') }}</textarea>
                @error("description.$localeCode")
                <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
            @endforeach

            <div class="mb-3">
                <label class="form-label">{{ __('messages.category') }}</label>
                <select name="category_id" class="form-select @error('category_id') is-invalid @enderror">
                    <option value="">{{ __('messages.select_category') }}</option>
                    @foreach ($categories as $category)
                    <option value="{{ $category->id }}" {{ old('category_id', $subcategory->category_id) == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                    @endforeach
                </select>

                @error('category_id')
                <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            {{-- Image --}}
            <div class="mb-3">
                <label class="form-label">{{ __('messages.image') }}</label>
                <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/*">
                @error('image')
                <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror


                {{-- Check if image exists and display it --}}
                @if($subcategory->image)
                <div class="mt-2">
                    <img src="{{ asset($subcategory->image) }}" width="80" alt="current image">
                    <small class="text-white d-block mt-1">{{ basename($subcategory->image) }}</small>
                </div>
                @endif
            </div>

            <button type="submit" class="btn btn-primary">{{ __('messages.update') }}</button>
            <a href="{{ route('subcategories.index') }}" class="btn btn-secondary">{{ __('messages.cancel') }}</a>
        </form>
    </div>
</div>
@endsection