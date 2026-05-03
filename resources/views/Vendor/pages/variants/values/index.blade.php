{{-- resources/views/variants/values/index.blade.php --}}
@extends('Vendor.layout.app')

@section('title', __('messages.Variant Values'))

@section('content')
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        @php
        $loc = app()->getLocale();
        $variantName = method_exists($variant, 'getTranslation')
        ? $variant->getTranslation('name', $loc, true)
        : (is_array($variant->name ?? null) ? ($variant->name[$loc] ?? reset($variant->name)) : ($variant->name ?? ('#'.$variant->id)));
        @endphp

        <h4 class="fw-bold py-3 mb-4 text-center">
            {{ __('messages.Variant Values') }} — {{ $variantName }} (ID: {{ $variant->id }})
        </h4>

        <div class="card">
            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <form method="GET" action="{{ route('vendor.variant-values.index', $variant->id) }}" id="searchForm" class="d-flex" style="gap:10px;">
                        <input
                            type="text"
                            name="search"
                            id="searchInput"
                            class="form-control bg-light text-dark"
                            placeholder="{{ __('Search by value name') }}"
                            value="{{ request('search') }}"
                            style="width:250px;">
                    </form>

                    <div>
                        <a href="{{ route('vendor.variant-values.create', $variant->id) }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> {{ __('messages.Add Value+') }}
                        </a>
                    </div>
                </div>

                <div class="table-responsive text-nowrap">
                    <table class="table table-striped text-black text-center">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ __('messages.Name') }}</th>
                                <th>Meta</th>
                                <th>{{ __('messages.variant') }}</th>
                                <th>{{ __('messages.Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($values as $item)
                            @php
                            $nameText = method_exists($item, 'getTranslation')
                            ? $item->getTranslation('name', $loc, true)
                            : (is_array($item->name) ? ($item->name[$loc] ?? reset($item->name)) : $item->name);
                            $metaPreview = $item->meta ? json_encode($item->meta, JSON_UNESCAPED_UNICODE) : '-';
                            $rowIndex = ($values->currentPage() - 1) * $values->perPage() + $loop->iteration;
                            @endphp
                            <tr>
                                <td>{{ $rowIndex }}</td>
                                <td>{{ $nameText }}</td>
                                <td style="max-width:320px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                    {{ $metaPreview }}
                                </td>
                                <td>{{ optional($item->variant)->name_text ?? optional($item->variant)->name ?? '—' }}</td>
                                <td>

                                    <form action="{{ route('vendor.variant-values.destroy', $item->id) }}" method="POST" class="d-inline-block">
                                        @csrf @method('DELETE')
                                        <input type="hidden" name="variant_id" value="{{ $variantId ?? request('variant_id') }}">
                                        <button type="submit" onclick="return confirm('{{ __('messages.confirm_delete') }}')" class="btn btn-sm btn-danger">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>


                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center">{{ __('messages.no_data') }}</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-3">
                    {{ $values->links('pagination::bootstrap-4') }}
                </div>

                <a href="{{ route('vendor.variants.index') }}" class="btn btn-secondary">{{ __('messages.Back') }}</a>
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
        let t = null;
        input.addEventListener('input', function() {
            clearTimeout(t);
            t = setTimeout(() => form.submit(), 500);
        });
    });
</script>
@endpush