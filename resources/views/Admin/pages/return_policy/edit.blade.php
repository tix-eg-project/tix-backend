@extends('Admin.layout.app')

@section('title', __('messages.return_policy'))

@section('content')
<div class="card">
    <div class="card-body">
        <h5 class="card-title">{{ __('messages.return_policy') }}</h5>

        <form method="POST" action="{{ route('admin.return-policy.update') }}">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label">{{ __('messages.content_ar') }}</label>
                <textarea id="editor_ar" name="content_ar" class="form-control @error('content_ar') is-invalid @enderror" rows="5">{{ old('content_ar', $policy->content_ar) }}</textarea>
                @error('content_ar')
                <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">{{ __('messages.content_en') }}</label>
                <textarea id="editor_en" name="content_en" class="form-control @error('content_en') is-invalid @enderror" rows="5">{{ old('content_en', $policy->content_en) }}</textarea>
                @error('content_en')
                <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">{{ __('messages.update') }}</button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.ckeditor.com/4.22.1/full/ckeditor.js"></script>
<script>
    CKEDITOR.replace('editor_ar', {
        language: 'ar',
        height: 300
    });

    CKEDITOR.replace('editor_en', {
        language: 'en',
        height: 300
    });
</script>
@endpush