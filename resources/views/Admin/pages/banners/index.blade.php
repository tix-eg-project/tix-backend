@extends('Admin.layout.app')

{{-- العنوان للـ <title> وكمان لو في هيدر داخلي بيقرأ page_title --}}
@section('title', __('messages.banners'))
@section('page_title', __('messages.banners'))

@section('banner_active', 'active')
@section('banner_open', 'open')

@section('content')
<div class="container-xxl container-p-y">

    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
        <h4 class="mb-0">{{ __('messages.banners') }}</h4>
        <a href="{{ route('banners.create') }}" class="btn btn-primary">
            <i class="bx bx-plus"></i> {{ __('messages.add_banner') }}
        </a>
    </div>

    {{-- بحث + فلتر (مفيدين هنا) --}}
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('banners.index') }}" id="searchForm">
                <div class="row g-2 align-items-center">
                    <div class="col-12 col-md-6">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bx bx-search"></i></span>
                            <input type="text" name="search" class="form-control"
                                placeholder="{{ __('messages.search') }}"
                                value="{{ request('search') }}">
                            @if(request('search'))
                            <a href="{{ route('banners.index', array_filter(request()->except('search'))) }}"
                                class="btn btn-outline-secondary">
                                {{ __('messages.clear') }}
                            </a>
                            @endif
                        </div>
                    </div>

                    <div class="col-12 col-md-4">
                        <select name="vendor_id" class="form-select">
                            <option value="">{{ __('messages.all_vendors') }}</option>
                            @foreach($vendors as $vendor)
                            <option value="{{ $vendor->id }}" @selected((string)request('vendor_id')===(string)$vendor->id)>
                                {{ $vendor->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12 col-md-2 text-end">
                        <button class="btn btn-secondary w-100">
                            <i class="bx bx-filter-alt"></i> {{ __('messages.filter') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width:56px">#</th>
                        <th>{{ __('messages.image') }}</th>
                        <th>{{ __('messages.title') }}</th>
                        <th>{{ __('messages.vendor') }}</th>
                        <th>{{ __('messages.created_at') }}</th>
                        <th class="text-end" style="width:140px">{{ __('messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($banners as $item)
                    <tr>
                        <td>{{ $loop->iteration + ($banners->currentPage()-1) * $banners->perPage() }}</td>
                        <td>
                            @if($item->image)
                            <img src="{{ asset($item->image) }}" alt="banner" class="rounded"
                                style="width:72px;height:40px;object-fit:cover">
                            @else
                            <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            {{-- يحاول يجيب الترجمة لو فيه translatable، وإلا fallback --}}
                            @php
                            $loc = app()->getLocale();
                            $title = method_exists($item, 'getTranslation')
                            ? ($item->getTranslation('title', $loc) ?? null)
                            : null;
                            if (!$title) {
                            $raw = is_string($item->title) ? json_decode($item->title, true) : $item->title;
                            $title = is_array($raw) ? ($raw[$loc] ?? reset($raw)) : ($item->title ?? null);
                            }
                            @endphp
                            {{ $title ?: '-' }}
                        </td>
                        <td>{{ $item->vendor?->name ?? __('messages.general') }}</td>
                        <td>{{ $item->created_at?->format('Y-m-d') }}</td>
                        <td class="text-end">
                            {{-- الأزرار المطلوبة بالحرف --}}
                            <a href="{{ route('banners.edit', $item->id) }}" class="btn btn-sm btn-primary">
                                <i class="fa-regular fa-pen-to-square"></i>
                            </a>
                            <form action="{{ route('banners.destroy', $item->id) }}" method="POST" class="d-inline-block">
                                @csrf @method('DELETE')
                                <button onclick="return confirm('{{ __('messages.confirm_delete') }}')"
                                    class="btn btn-sm btn-danger">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">{{ __('messages.no_data') }}</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($banners instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div class="card-body">
            {{ $banners->appends(request()->query())->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</div>
@endsection