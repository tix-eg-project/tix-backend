@extends('Admin.layout.app')

@section('social_links_active', 'active')
@section('social_links_open', 'open')
@section('title', __('messages.social_links'))

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">{{ __('messages.social_links') }}</h4>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('messages.close') }}"></button>
</div>
@endif

<div class="card rounded-4 custom-card bg-light text-dark">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>{{ __('messages.platform') }}</th>
                        <th>{{ __('messages.link') }}</th>
                        <th class="text-end">{{ __('messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($links as $link)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $link->platform }}</td>
                        <td>
                            <a href="{{ $link->url }}" target="_blank" class="text-decoration-underline">
                                {{ $link->url }}
                            </a>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('social-links.edit', $link->id) }}" class="btn btn-sm btn-warning">
                                <i class="bi bi-pencil-square me-1"></i>{{ __('messages.edit') }}
                            </a>

                            <form action="{{ route('social-links.destroy', $link->id) }}" method="POST" class="d-inline-block">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger"
                                    onclick="return confirm('{{ __('messages.confirm_delete') }}')">
                                    <i class="bi bi-trash me-1"></i>{{ __('messages.delete') }}
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted">
                            {{ __('messages.no_data') }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection