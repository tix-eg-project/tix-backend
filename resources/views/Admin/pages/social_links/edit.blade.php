@extends('Admin.layout.app')

@section('title', __('messages.edit_social_link'))

@section('content')
<div class="card">
    <div class="card-body">
        <h5 class="card-title">{{ __('messages.edit_social_link') }}</h5>
        @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        <form method="POST" action="{{ route('social-links.update', $link->id) }}">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label">{{ __('messages.platform') }}</label>
                <input type="text" name="platform" value="{{ old('platform', $link->platform) }}"
                    class="form-control @error('platform') is-invalid @enderror">
                @error('platform')
                <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">{{ __('messages.link') }}</label>
                <input type="url" name="url" value="{{ old('url', $link->url) }}"
                    class="form-control @error('url') is-invalid @enderror">
                @error('url')
                <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">{{ __('messages.update') }}</button>
            <a href="{{ route('social-links.index') }}" class="btn btn-secondary">{{ __('messages.cancel') }}</a>
        </form>
    </div>
</div>
@endsection