@extends('Admin.layout.app')
@section('products_active', 'active')
@section('title', __('messages.Products'))
@section('page_title', __('messages.Products'))

@section('content')
<div class="container-xxl container-p-y">

  <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
    <h4 class="mb-0">{{ __('messages.Products') }}</h4>
    <a href="{{ route('products.create') }}" class="btn btn-primary">
      <i class="bx bx-plus"></i> {{ __('messages.Add Product+') }}
    </a>
  </div>

  {{-- Search + Filters --}}
  <div class="card mb-3">
    <div class="card-body">
      <form method="GET" action="{{ route('products.index') }}" id="searchForm">
        <div class="row g-2 align-items-end">
          <div class="col-12 col-md-4">
            <label class="form-label">{{ __('messages.Search by Product name') }}</label>
            <div class="input-group">
              <span class="input-group-text"><i class="bx bx-search"></i></span>
              <input type="text" name="search" id="searchInput" class="form-control"
                placeholder="{{ __('messages.Search by Product name') }}"
                value="{{ request('search') }}">
              @if(request('search'))
              <a href="{{ route('products.index', collect(request()->except(['search','page']))->filter()->all()) }}"
                class="btn btn-outline-secondary">{{ __('messages.clear') }}</a>
              @endif
            </div>
          </div>

          <div class="col-6 col-md-2">
            <label class="form-label">{{ __('messages.category') }}</label>
            <select name="category_id" class="form-select" onchange="this.form.submit()">
              <option value="">{{ __('messages.all_categories') ?? __('messages.all') }}</option>
              @foreach(($categories ?? []) as $c)
              <option value="{{ $c->id }}" @selected((string)request('category_id')===(string)$c->id)>
                {{ $c->name_text ?? $c->name ?? ('#'.$c->id) }}
              </option>
              @endforeach
            </select>
          </div>

          <div class="col-6 col-md-2">
            <label class="form-label">{{ __('messages.subcategory') }}</label>
            <select name="subcategory_id" class="form-select" onchange="this.form.submit()">
              <option value="">{{ __('messages.all') }}</option>
              @foreach(($subcategories ?? []) as $sc)
              <option value="{{ $sc->id }}" @selected((string)request('subcategory_id')===(string)$sc->id)>
                {{ $sc->name_text ?? $sc->name ?? ('#'.$sc->id) }}
              </option>
              @endforeach
            </select>
          </div>

          <div class="col-6 col-md-2">
            <label class="form-label">{{ __('messages.brand') }}</label>
            <select name="brand_id" class="form-select" onchange="this.form.submit()">
              <option value="">{{ __('messages.all') }}</option>
              @foreach(($brands ?? []) as $b)
              <option value="{{ $b->id }}" @selected((string)request('brand_id')===(string)$b->id)>
                {{ $b->name_text ?? $b->name ?? ('#'.$b->id) }}
              </option>
              @endforeach
            </select>
          </div>

          <div class="col-6 col-md-2">
            <label class="form-label">{{ __('messages.status') }}</label>
            <select name="status" class="form-select" onchange="this.form.submit()">
              <option value="">{{ __('messages.all') }}</option>
              <option value="1" @selected(request('status')==='1' )>{{ __('messages.active') }}</option>
              <option value="2" @selected(request('status')==='2' )>{{ __('messages.inactive') }}</option>
            </select>
          </div>

          {{-- Vendor filter --}}
          <div class="col-6 col-md-2">
            <label class="form-label">{{ __('messages.vendor') }}</label>
            <select name="vendor_id" class="form-select" onchange="this.form.submit()">
              <option value="">{{ __('messages.all') }}</option>
              @foreach(($vendors ?? []) as $v)
              <option value="{{ $v->id }}" @selected((string)request('vendor_id')===(string)$v->id)>
                {{ $v->name_text ?? $v->name ?? ('#'.$v->id) }}
              </option>
              @endforeach
            </select>
          </div>
        </div>
      </form>
    </div>
  </div>


  <div class="card">
    <div class="table-responsive text-nowrap">
      <table class="table table-hover table-striped text-center align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th style="width:56px">#</th>
            <th>{{ __('messages.name') }}</th>
            <th>{{ __('messages.short_description') }}</th>
            <th>{{ __('messages.long_description') }}</th>
            <th>{{ __('messages.price') }}</th>
            <th>{{ __('messages.discount') }}</th>
            <th>{{ __('messages.category') }}</th>
            <th>{{ __('messages.subcategory') }}</th>
            <th>{{ __('messages.brand') }}</th>
            <th>{{ __('messages.status') }}</th>
            <th>{{ __('messages.vendor') }}</th>
            <th>{{ __('messages.images') }}</th>
            <th class="text-end" style="width:220px">{{ __('messages.Actions') }}</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($products as $product)
          @php
          $locale = app()->getLocale();
          $name = $product->name_text ?? (is_array($product->name ?? null) ? ($product->name[$locale] ?? reset($product->name)) : ($product->name ?? ''));
          $short = $product->short_description_text ?? (is_array($product->short_description ?? null) ? ($product->short_description[$locale] ?? reset($product->short_description)) : ($product->short_description ?? ''));
          $longRaw = $product->long_description ?? null;
          $long = is_array($longRaw) ? ($longRaw[$locale] ?? ($longRaw['en'] ?? reset($longRaw) ?? '')) : (string)($longRaw ?? '');
          @endphp
          <tr>
            <td>{{ $products->firstItem() ? $products->firstItem() + $loop->index : $loop->iteration }}</td>
            <td class="fw-semibold">{{ $name ?: '—' }}</td>
            <td class="text-muted">{{ \Illuminate\Support\Str::limit($short, 80) ?: '—' }}</td>
            <td class="text-muted">{{ \Illuminate\Support\Str::limit($long, 100) ?: '—' }}</td>
            <td>{{ $product->price }}</td>
            <td>{{ $product->discount ?? 0 }}</td>
            <td>{{ optional($product->category)->name_text ?? optional($product->category)->name ?? '—' }}</td>
            <td>{{ optional($product->subcategory)->name_text ?? optional($product->subcategory)->name ?? '—' }}</td>
            <td>{{ optional($product->brand)->name_text ?? optional($product->brand)->name ?? '—' }}</td>
            <td>
              <span class="badge {{ (int)$product->status === \App\Enums\Status::Active ? 'bg-success' : 'bg-secondary' }}">
                {{ \App\Enums\Status::getLabel((int)$product->status) }}
              </span>
            </td>
            <td>{{ optional($product->vendor)->name_text ?? optional($product->vendor)->name ?? '—' }}</td>
            <td style="white-space:nowrap; max-width: 260px; overflow:hidden;">
              @foreach (($product->image_urls ?? []) as $url)
              <img src="{{ $url }}" width="56" height="56" style="object-fit:cover;border-radius:6px;margin:2px;" alt="">
              @endforeach
            </td>
            <td class="text-end">
              <a href="{{ route('products-variant.create', $product) }}" class="btn btn-sm btn-info">
                <i class="bi bi-diagram-3"></i> {{ __('messages.Add Variant') ?? 'Add Variant' }}
              </a>

              @php
              $subCount = isset($product->variant_items_count)
              ? (int)$product->variant_items_count
              : (int)($product->relationLoaded('variantItems') ? $product->variantItems->count() : $product->variantItems()->count());
              @endphp
              <a href="{{ route('products-variant.index', $product) }}" class="btn btn-sm btn-outline-info position-relative" title="{{ __('messages.Sub Products') }}">
                <i class="bi bi-list-ul"></i> {{ __('messages.Sub Products') ?? 'Sub Products' }}
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary">{{ $subCount }}</span>
              </a>

              <a href="{{ route('products.edit', $product->id) }}" class="btn btn-sm btn-primary">
                <i class="fa-regular fa-pen-to-square"></i>
              </a>

              <form action="{{ route('products.delete', $product->id) }}" method="POST" class="d-inline">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('{{ __('messages.are_you_sure') ?? 'Are you sure?' }}')">
                  <i class="fa-solid fa-trash"></i>
                </button>
              </form>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="13" class="text-center text-muted py-4">{{ __('messages.no_products') ?? 'No products found' }}</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if($products instanceof \Illuminate\Pagination\LengthAwarePaginator)
    <div class="card-body">
      {{ $products->withQueryString()->links('pagination::bootstrap-5') }}
    </div>
    @endif
  </div>
</div>
@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('searchForm');
    const input = document.getElementById('searchInput');
    let t = null;
    if (input && form) {
      input.addEventListener('input', function() {
        clearTimeout(t);
        t = setTimeout(() => form.submit(), 500);
      });
    }
  });
</script>
@endpush