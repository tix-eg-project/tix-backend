@extends('Admin.layout.app')
@section('title', __('messages.add_banner'))

@section('content')
<div class="container-xxl container-p-y">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
        <h4 class="mb-0">{{ __('messages.add_banner') }}</h4>
        <a href="{{ route('banners.index') }}" class="btn btn-outline-secondary"><i class="bx bx-left-arrow-alt"></i> {{ __('messages.back') }}</a>
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
            <form method="POST" action="{{ route('banners.store') }}" enctype="multipart/form-data" class="row g-3">
                @csrf

                <div class="col-12 col-md-6">
                    <label class="form-label">{{ __('messages.vendor') }}</label>
                    <select name="vendor_id" class="form-select @error('vendor_id') is-invalid @enderror">
                        <option value="">{{ __('messages.general_banner') }}</option>
                        @foreach($vendors as $vendor)
                        <option value="{{ $vendor->id }}" @selected(old('vendor_id')==$vendor->id)>{{ $vendor->name }}</option>
                        @endforeach
                    </select>
                    @error('vendor_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>

                @foreach (LaravelLocalization::getSupportedLocales() as $localeCode => $locale)
                <div class="col-12 col-md-6">
                    <label class="form-label">{{ __('messages.title') }} ({{ strtoupper($localeCode) }})</label>
                    <input type="text" name="title[{{ $localeCode }}]" class="form-control @error(" title.$localeCode") is-invalid @enderror" value="{{ old("title.$localeCode") }}">
                    @error("title.$localeCode") <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>
                <div class="col-12">
                    <label class="form-label">{{ __('messages.description') }} ({{ strtoupper($localeCode) }})</label>
                    <textarea name="description[{{ $localeCode }}]" rows="3" class="form-control @error(" description.$localeCode") is-invalid @enderror">{{ old("description.$localeCode") }}</textarea>
                    @error("description.$localeCode") <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>
                @endforeach

                <div class="col-12 col-md-6">
                    <label class="form-label">{{ __('messages.image') }}</label>
                    <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/*">
                    @error('image') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>

                <div class="col-12 d-flex gap-2">
                    <button type="submit" class="btn btn-primary"><i class="bx bx-save"></i> {{ __('messages.save') }}</button>
                    <a href="{{ route('banners.index') }}" class="btn btn-light">{{ __('messages.cancel') }}</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection