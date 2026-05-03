@extends('Admin.layout.app')

@section('title', __('messages.edit_subcategory'))
@section('page_title', __('messages.edit_subcategory'))

@section('content')
<div class="container-xxl container-p-y">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
        <h4 class="mb-0">{{ __('messages.edit_subcategory') }}</h4>
        <a href="{{ route('subcategories.index') }}" class="btn btn-outline-secondary">
            <i class="bx bx-left-arrow-alt"></i> {{ __('messages.back') }}
        </a>
    </div>

    @if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('subcategories.update', $subcategory->id) }}" enctype="multipart/form-data" class="row g-3">
                @csrf
                @method('PUT')

                {{-- Helper مرن للترجمات --}}
                @php
                $resolveTrans = function($model, $field, $locale) {
                if (method_exists($model, 'getTranslation')) {
                $v = $model->getTranslation($field, $locale);
                if (!is_null($v) && $v !== '') return $v;
                }
                $raw = $model->getRawOriginal($field);
                $decoded = is_string($raw) ? json_decode($raw, true) : null;
                if (is_array($decoded) && array_key_exists($locale, $decoded)) return $decoded[$locale];

                $casted = $model->$field;
                if (is_array($casted) && array_key_exists($locale, $casted)) return $casted[$locale];

                if (is_array($decoded) && count($decoded)) return reset($decoded);
                if (is_array($casted) && count($casted)) return reset($casted);
                return null;
                };
                @endphp

                {{-- Dynamic Translations --}}
                @foreach (LaravelLocalization::getSupportedLocales() as $localeCode => $locale)
                @php
                $oldName = old("name.$localeCode");
                $oldDesc = old("description.$localeCode");
                $valName = isset($oldName) ? $oldName : $resolveTrans($subcategory, 'name', $localeCode);
                $valDesc = isset($oldDesc) ? $oldDesc : $resolveTrans($subcategory, 'description', $localeCode);
                @endphp

                <div class="col-12 col-md-6">
                    <label class="form-label">{{ __('messages.name') }} ({{ strtoupper($localeCode) }})</label>
                    <input
                        type="text"
                        name="name[{{ $localeCode }}]"
                        class="form-control @error(" name.$localeCode") is-invalid @enderror"
                        value="{{ $valName }}">
                    @error("name.$localeCode") <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>

                <div class="col-12">
                    <label class="form-label">{{ __('messages.description') }} ({{ strtoupper($localeCode) }})</label>
                    <textarea
                        name="description[{{ $localeCode }}]"
                        class="form-control @error(" description.$localeCode") is-invalid @enderror"
                        rows="3">{{ $valDesc }}</textarea>
                    @error("description.$localeCode") <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>
                @endforeach

                <div class="col-12 col-md-6">
                    <label class="form-label">{{ __('messages.category') }}</label>
                    <select name="category_id" class="form-select @error('category_id') is-invalid @enderror">
                        <option value="">{{ __('messages.select_category') }}</option>
                        @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected(old('category_id', $subcategory->category_id) == $category->id)>
                            {{ $category->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('category_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label">{{ __('messages.image') }}</label>
                    <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/*">
                    @error('image') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror

                    @if($subcategory->image)
                    <div class="mt-2">
                        <img src="{{ asset($subcategory->image) }}" width="120" height="70" style="object-fit:cover" class="rounded border" alt="current image">
                        <small class="text-muted d-block mt-1">{{ basename($subcategory->image) }}</small>
                    </div>
                    @endif
                </div>

                <div class="col-12 d-flex gap-2">
                    <button type="submit" class="btn btn-primary"><i class="bx bx-save"></i> {{ __('messages.update') }}</button>
                    <a href="{{ route('subcategories.index') }}" class="btn btn-light">{{ __('messages.cancel') }}</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection