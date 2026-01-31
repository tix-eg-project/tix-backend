@extends('Admin.layout.app')
@section('title', __('messages.add_subcategory'))

@section('content')
<div class="card">
    <div class="card-body">
        <h5 class="card-title">{{ __('messages.add_subcategory') }}</h5>

        <form method="POST" action="{{ route('subcategories.store') }}" enctype="multipart/form-data">
            @csrf

            {{-- Dynamic Translations --}}
            @foreach (LaravelLocalization::getSupportedLocales() as $localeCode => $locale)
            <div class="mb-3">
                <label class="form-label">{{ __('messages.name') }} ({{ strtoupper($localeCode) }})</label>
                <input type="text" name="name[{{ $localeCode }}]" class="form-control @error(" name.$localeCode") is-invalid @enderror" value="{{ old("name.$localeCode") }}">
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
                <label class="form-label">{{ __('messages.category') }}</label>
                <select name="category_id" class="form-select @error('category_id') is-invalid @enderror">
                    <option value="">{{ __('messages.select_category') }}</option>
                    @foreach ($categories as $category)
                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
                @error('category_id')
                <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">{{ __('messages.image') }}</label>
                <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/*">
                @error('image')
                <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">{{ __('messages.save') }}</button>
            <a href="{{ route('subcategories.index') }}" class="btn btn-secondary">{{ __('messages.cancel') }}</a>
        </form>
    </div>
</div>
@endsection