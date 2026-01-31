@extends('Admin.layout.app')
@section('title', __('messages.add_category'))

@section('content')
<div class="card">
    <div class="card-body">
        <h5 class="card-title">{{ __('messages.add_category') }}</h5>

        @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('categories.store') }}" enctype="multipart/form-data">
            @csrf

            {{-- Dynamic Translations --}}
            @foreach (LaravelLocalization::getSupportedLocales() as $localeCode => $locale)
            <div class="mb-3">
                <label class="form-label">{{ __('messages.name') }} ({{ strtoupper($localeCode) }})</label>
                <input type="text" name="name[{{ $localeCode }}]" class="form-control @error('name.' . $localeCode) is-invalid @enderror" value="{{ old('name.' . $localeCode) }}">
                @error('name.' . $localeCode)
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
            <a href="{{ route('categories.index') }}" class="btn btn-secondary">{{ __('messages.cancel') }}</a>
        </form>
    </div>
</div>
@endsection