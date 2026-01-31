@extends('Admin.layout.app')

@section('title', __('messages.edit_banner'))
@section('page_title', __('messages.edit_banner'))

@section('content')
<div class="card">
    <div class="card-body">
        <h5 class="card-title">{{ __('messages.edit_banner') }}</h5>

        @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('banners.update', $banner->id) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- Vendor (اختياري) --}}
            <div class="mb-3">
                <label class="form-label">{{ __('messages.vendor') }}</label>
                <select name="vendor_id" class="form-select @error('vendor_id') is-invalid @enderror">
                    <option value="">{{ __('messages.general_banner') }}</option>
                    @foreach($vendors as $vendor)
                    <option value="{{ $vendor->id }}" @selected(old('vendor_id', $banner->vendor_id) == $vendor->id)>{{ $vendor->name }}</option>
                    @endforeach
                </select>
                @error('vendor_id')
                <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            {{-- Helpers للترجمات (مرن مع كذا تخزين) --}}
            @php
            $resolveTrans = function($model, $field, $locale) {
            // 1) لو عنده getTranslation (spatie)
            if (method_exists($model, 'getTranslation')) {
            $v = $model->getTranslation($field, $locale);
            if (!is_null($v) && $v !== '') return $v;
            }
            // 2) جرّب raw JSON
            $raw = $model->getRawOriginal($field);
            $decoded = is_string($raw) ? json_decode($raw, true) : null;
            if (is_array($decoded) && array_key_exists($locale, $decoded)) {
            return $decoded[$locale];
            }
            // 3) جرّب الـ cast (لو متعرّف في $casts)
            $casted = $model->$field;
            if (is_array($casted) && array_key_exists($locale, $casted)) {
            return $casted[$locale];
            }
            // 4) fallback: أول قيمة موجودة
            if (is_array($decoded) && count($decoded)) return reset($decoded);
            if (is_array($casted) && count($casted)) return reset($casted);
            return null;
            };
            @endphp

            {{-- Dynamic Translations --}}
            @foreach (LaravelLocalization::getSupportedLocales() as $localeCode => $locale)
            @php
            $oldTitle = old("title.$localeCode");
            $oldDesc = old("description.$localeCode");
            $valTitle = isset($oldTitle) ? $oldTitle : $resolveTrans($banner, 'title', $localeCode);
            $valDesc = isset($oldDesc) ? $oldDesc : $resolveTrans($banner, 'description', $localeCode);
            @endphp

            <div class="mb-3">
                <label class="form-label">{{ __('messages.title') }} ({{ strtoupper($localeCode) }})</label>
                <input
                    type="text"
                    name="title[{{ $localeCode }}]"
                    class="form-control @error(" title.$localeCode") is-invalid @enderror"
                    value="{{ $valTitle }}">
                @error("title.$localeCode")
                <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">{{ __('messages.description') }} ({{ strtoupper($localeCode) }})</label>
                <textarea
                    name="description[{{ $localeCode }}]"
                    class="form-control @error(" description.$localeCode") is-invalid @enderror"
                    rows="3">{{ $valDesc }}</textarea>
                @error("description.$localeCode")
                <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>
            @endforeach

            {{-- Image --}}
            <div class="mb-3">
                <label class="form-label">{{ __('messages.image') }}</label>
                <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/*">
                @error('image') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror

                @if($banner->image)
                <div class="mt-2">
                    <img src="{{ asset($banner->image) }}" width="120" height="70" style="object-fit:cover" class="rounded border" alt="current image">
                    <small class="text-muted d-block mt-1">{{ basename($banner->image) }}</small>
                </div>
                @endif
            </div>

            <button type="submit" class="btn btn-primary">{{ __('messages.update') }}</button>
            <a href="{{ route('banners.index') }}" class="btn btn-secondary">{{ __('messages.cancel') }}</a>
        </form>
    </div>
</div>
@endsection