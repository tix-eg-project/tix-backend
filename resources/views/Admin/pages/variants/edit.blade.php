{{-- resources/views/variants/edit.blade.php --}}
@extends('Admin.layout.app')

@section('title', __('messages.Update Variant'))

@section('content')
<div class="card">
    <div class="card-body">
        <h5 class="card-title text-center">{{ __('messages.Update Variant') }}</h5>

        <form method="POST" action="{{ route('variants.update', $variant->id) }}">
            @csrf
            @method('PUT')

            {{-- Dynamic Translations --}}
            @foreach (LaravelLocalization::getSupportedLocales() as $localeCode => $locale)
            @php
            $raw = json_decode($variant->getRawOriginal('name') ?? '[]', true);
            $val = old("name.$localeCode") ?? ($raw[$localeCode] ?? '');
            @endphp
            <div class="mb-3">
                <label class="form-label">{{ __('messages.name') }} ({{ strtoupper($localeCode) }})</label>
                <input
                    type="text"
                    name="name[{{ $localeCode }}]"
                    class="form-control @error(" name.$localeCode") is-invalid @enderror"
                    value="{{ $val }}">
                @error("name.$localeCode")
                <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
            @endforeach

            <button type="submit" class="btn btn-primary">{{ __('messages.update') }}</button>
            <a href="{{ route('variants.index') }}" class="btn btn-secondary">{{ __('messages.cancel') }}</a>
        </form>
    </div>
</div>
@endsection