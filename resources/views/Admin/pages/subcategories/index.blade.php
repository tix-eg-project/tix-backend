@extends('Admin.layout.app')

@section('title', __('messages.subcategories'))
@section('page_title', __('messages.subcategories'))
@section('subcategories_active', 'active')

@section('content')
<div class="container-xxl container-p-y">

    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
        <h4 class="mb-0">{{ __('messages.subcategories') }}</h4>
        <a href="{{ route('subcategories.create') }}" class="btn btn-primary">
            <i class="bx bx-plus"></i> {{ __('messages.add_subcategory') }}
        </a>
    </div>

    {{-- بحث + فلتر (حسب القسم) --}}
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('subcategories.index') }}" id="searchForm">
                <div class="row g-2 align-items-center">
                    <div class="col-12 col-md-6">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bx bx-search"></i></span>
                            <input
                                type="text"
                                name="search"
                                class="form-control"
                                placeholder="{{ __('messages.search') }}"
                                value="{{ request('search') }}">
                            @if(request('search'))
                            <a href="{{ route('subcategories.index', collect(request()->except(['search','page']))->filter()->all()) }}"
                                class="btn btn-outline-secondary">
                                {{ __('messages.clear') }}
                            </a>
                            @endif
                        </div>
                    </div>

                    <div class="col-12 col-md-4">
                        <select name="category_id" class="form-select">
                            <option value="">{{ __('messages.all_categories') }}</option>
                            @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" @selected((string)request('category_id')===(string)$cat->id)>
                                {{ $cat->name }}
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
            <table class="table table-hover table-striped mb-0 text-center">
                <thead class="table-light">
                    <tr>
                        <th style="width:56px">#</th>
                        <th>{{ __('messages.name') }}</th>
                        <th>{{ __('messages.description') }}</th>
                        <th>{{ __('messages.category') }}</th>
                        <th class="text-end" style="width:140px">{{ __('messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($subcategories as $item)
                    @php
                    $loc = app()->getLocale();

                    // name
                    $name = method_exists($item, 'getTranslation') ? ($item->getTranslation('name', $loc) ?? null) : null;
                    if (!$name) {
                    $raw = is_string($item->name) ? json_decode($item->name, true) : $item->name;
                    $name = is_array($raw) ? ($raw[$loc] ?? reset($raw)) : ($item->name ?? null);
                    }

                    // description (مختصر)
                    $desc = method_exists($item, 'getTranslation') ? ($item->getTranslation('description', $loc) ?? null) : null;
                    if (!$desc) {
                    $rawd = is_string($item->description) ? json_decode($item->description, true) : $item->description;
                    $desc = is_array($rawd) ? ($rawd[$loc] ?? reset($rawd)) : ($item->description ?? null);
                    }
                    $descShort = \Illuminate\Support\Str::limit(strip_tags((string)$desc), 60);
                    @endphp

                    <tr>
                        <td>{{ $loop->iteration + ($subcategories->currentPage()-1) * $subcategories->perPage() }}</td>
                        <td>{{ $name ?: '-' }}</td>
                        <td class="text-muted">{{ $descShort ?: '—' }}</td>
                        <td>{{ $item->category?->name ?? '—' }}</td>
                        <td class="text-end">
                            <a href="{{ route('subcategories.edit', $item->id) }}" class="btn btn-sm btn-primary">
                                <i class="fa-regular fa-pen-to-square"></i>
                            </a>
                            <form action="{{ route('subcategories.destroy', $item->id) }}" method="POST" class="d-inline-block">
                                @csrf @method('DELETE')
                                <button onclick="return confirm('{{ __('messages.confirm_delete') }}')" class="btn btn-sm btn-danger">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">{{ __('messages.no_data') }}</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($subcategories instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div class="card-body">
            {{ $subcategories->appends(request()->query())->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</div>
@endsection