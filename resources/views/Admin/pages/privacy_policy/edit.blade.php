@extends('Admin.layout.app')

@section('title', __('messages.privacy_policy'))

@section('content')
<div class="card">
    <div class="card-body">
        <h5 class="card-title">{{ __('messages.privacy_policy') }}</h5>

        <form method="POST" action="{{ route('admin.privacy-policy.update') }}">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label">{{ __('messages.content_ar') }}</label>
                <textarea name="content_ar" class="form-control @error('content_ar') is-invalid @enderror" rows="5">{{ old('content_ar', $policy->content_ar) }}</textarea>
                @error('content_ar')
                <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">{{ __('messages.content_en') }}</label>
                <textarea name="content_en" class="form-control @error('content_en') is-invalid @enderror" rows="5">{{ old('content_en', $policy->content_en) }}</textarea>
                @error('content_en')
                <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">{{ __('messages.update') }}</button>
        </form>
    </div>
</div>
@endsection