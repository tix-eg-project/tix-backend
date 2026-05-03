@extends('Admin.layout.app')
@section('title', __('messages.vsoft_cities_mapping'))
@section('page_title', __('messages.vsoft_cities_mapping'))

@push('styles')
<style>
  .hero {
    border-radius: 16px;
    background: linear-gradient(135deg, #0ea5ea 0%, #6a5af9 100%);
    color: #fff;
    padding: 18px 20px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, .08)
  }

  .chip {
    display: inline-flex;
    align-items: center;
    gap: .45rem;
    padding: .35rem .7rem;
    border-radius: 999px;
    border: 1px solid rgba(255, 255, 255, .35);
    backdrop-filter: blur(4px);
    font-weight: 600
  }

  .card-clean {
    border: 1px solid rgba(0, 0, 0, .08);
    border-radius: 16px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, .06);
    background: #fff
  }

  .table thead th {
    background: #f7f7fb !important;
    color: #424750;
    font-weight: 700;
    border: 0 !important
  }

  .table tbody td {
    vertical-align: middle;
    border-color: #efeff3 !important;
    color: #2b2b2b
  }
</style>
@endpush

@section('content')
<div class="container-xxl container-p-y">

  {{-- Hero --}}
  <div class="hero d-flex justify-content-between align-items-center mb-3">
    <div>
      <h4 class="mb-1">{{ __('messages.vsoft_cities_mapping') }}</h4>
      <div class="d-flex flex-wrap gap-2">
        <span class="chip"><i class="bi bi-geo-alt"></i> {{ $cities->total() }} {{ __('messages.records') }}</span>
        <span class="chip"><i class="bi bi-filter"></i> {{ __('messages.search') }}: {{ request('q','—') }}</span>
      </div>
    </div>
    <div class="text-end">
      <button class="btn btn-light text-dark" data-bs-toggle="modal" data-bs-target="#bulkMapModal">
        <i class="bx bx-link-alt me-1"></i> {{ __('messages.bulk_map_to_zone') }}
      </button>
    </div>
  </div>

  {{-- Alerts --}}
  @if(session('ok'))
  <div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="bi bi-check-circle me-2"></i>{{ session('ok') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('messages.close') }}"></button>
  </div>
  @endif

  {{-- Filters --}}
  <div class="card mb-3">
    <div class="card-body">
      <form method="GET" action="{{ route('admin.vsoft-cities.index') }}" id="filterForm">
        <div class="row g-2 align-items-end">
          <div class="col-12 col-md-3">
            <label class="form-label">{{ __('messages.search') }}</label>
            <div class="input-group">
              <span class="input-group-text"><i class="bx bx-search"></i></span>
              <input type="text" name="q" id="q" class="form-control"
                value="{{ request('q') }}" placeholder="{{ __('messages.search_city') }}">
            </div>
          </div>

          <div class="col-12 col-md-3">
            <label class="form-label">{{ __('messages.mapping_status') }}</label>
            <select class="form-select" name="mapping" id="mapping">
              <option value="">{{ __('messages.all') }}</option>
              <option value="mapped" @selected(request('mapping')==='mapped' )>{{ __('messages.mapped') }}</option>
              <option value="unmapped" @selected(request('mapping')==='unmapped' )>{{ __('messages.unmapped') }}</option>
            </select>
          </div>

          <div class="col-12 col-md-3">
            <label class="form-label">{{ __('messages.vsoft_zone') }}</label>
            <select class="form-select" name="vsoft_zone_id" id="vsoft_zone_id">
              <option value="">{{ __('messages.all') }}</option>
              @foreach($vsoftZones as $vz)
              <option value="{{ $vz }}" @selected((string)request('vsoft_zone_id')===(string)$vz)>{{ $vz }}</option>
              @endforeach
            </select>
          </div>

          <div class="col-12 col-md-3">
            <label class="form-label">{{ __('messages.local_shipping_zone') }}</label>
            <select class="form-select" name="shipping_zone_id" id="shipping_zone_id">
              <option value="">{{ __('messages.all') }}</option>
              @foreach($zones as $z)
              @php
              // استخراج الاسم مهما كان JSON أو نص
              $loc = app()->getLocale();
              $zName = $z->name ?? '—';
              if (is_array($zName)) { $zName = $zName[$loc] ?? reset($zName); }
              @endphp
              <option value="{{ $z->id }}" @selected((string)request('shipping_zone_id')===(string)$z->id)>
                {{ $z->id }} — {{ $zName }}
              </option>
              @endforeach
            </select>
          </div>

          <div class="col-12 col-md-3 mt-2">
            <label class="form-label">{{ __('messages.per_page') }}</label>
            <select class="form-select" name="per_page" id="per_page">
              @foreach([25,50,100,200] as $pp)
              <option value="{{ $pp }}" @selected((int)request('per_page',50)===$pp)>{{ $pp }}</option>
              @endforeach
            </select>
          </div>

          <div class="col-12 col-md-9 text-end mt-2">
            <button class="btn btn-secondary">
              <i class="bx bx-filter-alt"></i> {{ __('messages.apply_filters') }}
            </button>
            <a href="{{ route('admin.vsoft-cities.index') }}" class="btn btn-outline-secondary">
              {{ __('messages.clear') }}
            </a>
          </div>
        </div>
      </form>
    </div>
  </div>

  {{-- Table --}}
  <form method="POST" action="{{ route('admin.vsoft-cities.bulk-map') }}" id="bulkForm">
    @csrf
    <div class="card-clean p-0">
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead>
            <tr>
              <th style="width:42px"><input type="checkbox" id="checkAll"></th>
              <th style="width:72px">#</th>
              <th>{{ __('messages.vsoft_city_id') }}</th>
              <th>{{ __('messages.name') }}</th>
              <th>{{ __('messages.vsoft_zone') }}</th>
              <th>{{ __('messages.local_shipping_zone') }}</th>
              <th class="text-end">{{ __('messages.actions') }}</th>
            </tr>
          </thead>
          <tbody>
            @forelse($cities as $c)
            @php
            // اسم منطقة الشحن المحلية — يشتغل لو name JSON أو نص
            $zoneName = '—';
            if ($c->shippingZone) {
            // لو عندك accessor في الموديل: $c->shippingZone->name_text
            if (method_exists($c->shippingZone, 'getAttribute') && !empty($c->shippingZone->name_text ?? null)) {
            $zoneName = $c->shippingZone->name_text;
            } else {
            $raw = $c->shippingZone->name ?? null;
            if (is_array($raw)) {
            $loc = app()->getLocale();
            $zoneName = $raw[$loc] ?? reset($raw) ?? '—';
            } else {
            $zoneName = $raw ?? '—';
            }
            }
            }
            @endphp
            <tr>
              <td><input type="checkbox" name="city_ids[]" value="{{ $c->id }}" class="row-check"></td>
              <td>{{ $cities->firstItem() + $loop->index }}</td>
              <td>{{ $c->vsoft_city_id }}</td>
              <td>{{ $c->name }}</td>
              <td>{{ $c->vsoft_zone_id ?? '—' }}</td>
              <td>{{ $zoneName }}</td>
              <td class="text-end">
                <a href="{{ route('admin.vsoft-cities.edit',$c->id) }}" class="btn btn-sm btn-primary">
                  <i class="fa-regular fa-pen-to-square"></i> {{ __('messages.edit') }}
                </a>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="7" class="text-center text-muted py-4">{{ __('messages.no_data_found') }}</td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="p-3">
        {!! $cities->withQueryString()->links('pagination::bootstrap-5') !!}
      </div>
    </div>
  </form>
</div>

{{-- Bulk Map Modal --}}
<div class="modal fade" id="bulkMapModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form method="POST" action="{{ route('admin.vsoft-cities.bulk-map') }}" class="modal-content">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title">{{ __('messages.bulk_map_to_zone') }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('messages.close') }}"></button>
      </div>
      <div class="modal-body">
        <p class="text-muted">{{ __('messages.bulk_map_help') }}</p>
        <div class="mb-3">
          <label class="form-label">{{ __('messages.local_shipping_zone') }}</label>
          <select name="shipping_zone_id" class="form-select">
            <option value="">{{ __('messages.none') }} — ({{ __('messages.remove_mapping') }})</option>
            @foreach($zones as $z)
            @php
            $loc = app()->getLocale();
            $zName = $z->name ?? '—';
            if (is_array($zName)) { $zName = $zName[$loc] ?? reset($zName); }
            @endphp
            <option value="{{ $z->id }}">{{ $z->id }} — {{ $zName }}</option>
            @endforeach
          </select>
        </div>
        <div id="bulkSelectedContainer"></div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-primary">{{ __('messages.apply') }}</button>
      </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('filterForm');
    const q = document.getElementById('q');
    let t = null;
    if (q) {
      q.addEventListener('input', () => {
        clearTimeout(t);
        t = setTimeout(() => form.submit(), 500);
      });
    }

    const checkAll = document.getElementById('checkAll');
    const rowChecks = document.querySelectorAll('.row-check');
    if (checkAll) {
      checkAll.addEventListener('change', function() {
        rowChecks.forEach(ch => ch.checked = checkAll.checked);
      });
    }

    const bulkModal = document.getElementById('bulkMapModal');
    bulkModal.addEventListener('show.bs.modal', function() {
      const container = document.getElementById('bulkSelectedContainer');
      container.innerHTML = '';
      const selected = Array.from(document.querySelectorAll('.row-check:checked')).map(i => i.value);
      if (selected.length === 0) {
        container.innerHTML = '<div class="alert alert-warning mb-0">{{ __('
        messages.no_rows_selected ') }}</div>';
        return;
      }
      selected.forEach(id => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'city_ids[]';
        input.value = id;
        container.appendChild(input);
      });
    });
  });
</script>
@endpush