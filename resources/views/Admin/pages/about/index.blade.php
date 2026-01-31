@extends('Admin.layout.app')

@section('title', __('messages.about_us'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-0">{{ __('messages.about_us') }}</h4>
        <small class="text-muted">{{ __('messages.last_update') }}:
            @if($about && $about->updated_at)
            {{ $about->updated_at->diffForHumans() }}
            @else
            —
            @endif
        </small>
    </div>

    <a href="{{ route('about.edit') }}" class="btn btn-primary">
        <i class="bi bi-pencil-square me-1"></i> {{ __('messages.edit') }}
    </a>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('messages.close') }}"></button>
</div>
@endif

@if($about)
<div class="card rounded-4 custom-card bg-light text-dark border-0 shadow-sm">
    <div class="card-body p-4">
        <div class="row g-4 align-items-start">
            <!-- النصوص -->
            <div class="col-lg-8">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                        {{ __('messages.title') }}
                    </span>
                    <h5 class="mb-0 fw-semibold">
                        {{ $about->getTranslation('title', app()->getLocale()) ?: '—' }}
                    </h5>
                </div>

                <div class="mb-3">
                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">
                        {{ __('messages.description') }}
                    </span>
                    <div class="mt-2 text-body" style="white-space: pre-wrap; line-height: 1.8;">
                        {{ $about->getTranslation('description', app()->getLocale()) ?: '—' }}
                    </div>
                </div>

                <div class="d-flex flex-wrap gap-2">
                    <span class="badge bg-light text-muted border">
                        <i class="bi bi-translate me-1"></i> {{ strtoupper(app()->getLocale()) }}
                    </span>
                    @if($about->created_at)
                    <span class="badge bg-light text-muted border">
                        <i class="bi bi-clock-history me-1"></i> {{ $about->created_at->format('Y-m-d') }}
                    </span>
                    @endif
                </div>
            </div>

            <!-- الصورة -->
            <div class="col-lg-4">
                <div class="ratio ratio-4x3 rounded-4 overflow-hidden border bg-white d-flex align-items-center justify-content-center">
                    @if($about->image)
                    <img src="{{ asset($about->image) }}" alt="About image"
                        class="w-100 h-100 object-fit-cover">
                    @else
                    <div class="text-center text-muted p-3">
                        <i class="bi bi-image fs-1 d-block mb-2"></i>
                        <span>{{ __('messages.no_data') }}</span>
                    </div>
                    @endif
                </div>

                <div class="d-grid mt-3">
                    <a href="{{ route('about.edit') }}" class="btn btn-outline-primary">
                        <i class="bi bi-pencil me-1"></i> {{ __('messages.edit') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@else
<div class="alert alert-light text-muted border d-flex align-items-center" role="alert">
    <i class="bi bi-info-circle me-2"></i>
    <span>{{ __('messages.no_data') }}</span>
</div>
@endif
@endsection