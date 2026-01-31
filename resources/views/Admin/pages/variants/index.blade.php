{{-- resources/views/variants/index.blade.php --}}
@extends('Admin.layout.app')

@section('variant_active', 'active')
@section('variant_open', 'open')

@section('title', __('messages.Variants'))

@section('content')
<div class="content-wrapper">
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4 text-center">
            <span class="text-muted fw-light"></span> {{ __('messages.Variants') }}
        </h4>

        <!-- Card -->
        <div class="card">
            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <!-- Left: Search -->
                    <form method="GET" action="{{ route('variants.index') }}" id="searchForm" class="d-flex" style="gap: 10px;">
                        <input type="text"
                            name="search"
                            id="searchInput"
                            class="form-control bg-light text-dark"
                            placeholder="{{ __('Search by variant name') }}"
                            value="{{ request('search') }}"
                            style="width: 250px;">
                    </form>

                    <!-- Right: Add Button -->
                    <div>
                        <a href="{{ route('variants.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> {{ __('messages.Add Variant+') }}
                        </a>
                    </div>
                </div>

                <div class="table-responsive text-nowrap">
                    <table class="table table-striped text-black text-center">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ __('messages.Name') }}</th>
                                <th>{{ __('messages.Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($variants as $item)
                            @php
                            // اسم حسب لغة الواجهة مع fallback آمن للعرض فقط
                            $locale = app()->getLocale();
                            if (method_exists($item, 'getTranslation')) {
                            $nameText = $item->getTranslation('name', $locale, true);
                            } else {
                            $raw = $item->name;
                            $nameText = is_array($raw) ? ($raw[$locale] ?? (reset($raw) ?: '')) : (string)$raw;
                            }
                            @endphp
                            <tr>
                                <td>{{ $loop->iteration + ($variants->currentPage() - 1) * $variants->perPage() }}</td>
                                <td>{{ $nameText }}</td>
                                <td>
                                    <a href="{{ route('variants.edit', $item->id) }}" class="btn btn-sm btn-primary me-1">
                                        <i class="fa-regular fa-pen-to-square"></i>
                                    </a>

                                    <a href="{{ route('variant-values.create', $item->id) }}"
                                        class="btn btn-sm btn-warning me-1" title="Add Value">
                                        <i class="bi bi-plus-square-dotted"></i>
                                    </a>

                                    <form action="{{ route('variants.destroy', $item->id) }}" method="POST" class="d-inline-block">
                                        @csrf @method('DELETE')
                                        <button onclick="return confirm('{{ __('messages.confirm_delete') }}')" class="btn btn-sm btn-danger">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center">{{ __('messages.no_data') }}</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-3">
                    {{ $variants->links('pagination::bootstrap-4') }}
                </div>

                <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">{{ __('messages.Back') }}</a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const input = document.getElementById('searchInput');
        const form = document.getElementById('searchForm');
        let timer = null;

        input.addEventListener('input', function() {
            clearTimeout(timer);
            timer = setTimeout(() => form.submit(), 500); // انتظر 0.5 ثانية بعد آخر حرف
        });
    });
</script>
@endpush