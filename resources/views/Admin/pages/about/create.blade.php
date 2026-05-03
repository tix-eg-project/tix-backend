@extends('Admin.layout.app')

@section('title', __('messages.add_about_us'))

@section('content')
<div class="card">
    <div class="card-body">
        <h5 class="card-title">{{ __('messages.add_about_us') }}</h5>

        <form method="POST" action="{{ route('about.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="mb-3">
                <label class="form-label">{{ __('messages.title_ar') }}</label>
                <input type="text" name="title[ar]" class="form-control @error('title.ar') is-invalid @enderror"
                    value="{{ old('title.ar') }}">
                @error('title.ar') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">{{ __('messages.title_en') }}</label>
                <input type="text" name="title[en]" class="form-control @error('title.en') is-invalid @enderror"
                    value="{{ old('title.en') }}">
                @error('title.en') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">{{ __('messages.description_ar') }}</label>
                <textarea name="description[ar]" class="form-control ckeditor-desc @error('description.ar') is-invalid @enderror"
                    rows="6">{{ old('description.ar') }}</textarea>
                @error('description.ar') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">{{ __('messages.description_en') }}</label>
                <textarea name="description[en]" class="form-control ckeditor-desc @error('description.en') is-invalid @enderror"
                    rows="6">{{ old('description.en') }}</textarea>
                @error('description.en') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">{{ __('messages.image') }}</label>
                <input type="file" name="image" class="form-control" accept="image/*" required>
                @error('image') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>

            <button type="submit" class="btn btn-primary">{{ __('messages.save') }}</button>
            <a href="{{ route('about.index') }}" class="btn btn-secondary">{{ __('messages.cancel') }}</a>
        </form>

    </div>
</div>

@push('scripts')
<script src="https://cdn.ckeditor.com/4.22.1/full/ckeditor.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const editors = document.querySelectorAll('.ckeditor-desc');
        editors.forEach(editor => {
            CKEDITOR.replace(editor, {
                language: '{{ app()->getLocale() }}',
                removePlugins: 'exportpdf',
            });
        });
    });
</script>
@endpush
@endsection
