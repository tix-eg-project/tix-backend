@extends('Admin.layout.app')

@section('stay_in_touch_active', 'active')
@section('stay_in_touch_open', 'open')
@section('title', __('messages.edit_stay_in_touch'))

@php
// Helpers صغيرة للّف لمصفوفة بأمان
$wrap = function($value) {
if (is_null($value) || $value === '') return [];
return is_array($value) ? $value : [$value];
};

// عناوين عربي
$addressAr = old('address_ar');
if (is_null($addressAr)) {
$addressAr = $wrap(optional($data)->address_ar ?? []);
}

// عناوين إنجليزي
$addressEn = old('address_en');
if (is_null($addressEn)) {
$addressEn = $wrap(optional($data)->address_en ?? []);
}

// تليفونات: ممكن تيجي JSON أو مصفوفة
$phones = old('phones');
if (is_null($phones)) {
$rawPhones = optional($data)->phones;
if (is_string($rawPhones)) {
$decoded = json_decode($rawPhones, true);
$phones = is_array($decoded) ? $decoded : $wrap($rawPhones);
} else {
$phones = $wrap($rawPhones);
}
}

// روابط ويب
$webLinks = old('web_link');
if (is_null($webLinks)) {
$webLinks = $wrap(optional($data)->web_link ?? []);
}
@endphp

@section('content')
<div class="card rounded-4 custom-card bg-light text-dark">
    <div class="card-body">
        <h5 class="card-title">{{ __('messages.edit_stay_in_touch') }}</h5>

        @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('messages.close') }}"></button>
        </div>
        @endif

        <form method="POST" action="{{ route('stay-in-touch.update') }}">
            @csrf
            @method('PUT')

            {{-- Address AR --}}
            <div class="mb-3">
                <label class="form-label">{{ __('messages.address_ar') }}</label>
                <div id="address-ar-container">
                    @forelse($addressAr as $item)
                    <div class="input-group mb-2">
                        <input type="text" name="address_ar[]" class="form-control" value="{{ $item }}">
                        <button type="button" class="btn btn-danger" onclick="removeInput(this)">×</button>
                    </div>
                    @empty
                    <div class="input-group mb-2">
                        <input type="text" name="address_ar[]" class="form-control">
                        <button type="button" class="btn btn-danger" onclick="removeInput(this)">×</button>
                    </div>
                    @endforelse
                </div>
                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="addDynamicInput('address-ar-container', 'address_ar[]')">
                    + {{ __('messages.add_address_ar') }}
                </button>
                @error('address_ar') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>

            {{-- Address EN --}}
            <div class="mb-3">
                <label class="form-label">{{ __('messages.address_en') }}</label>
                <div id="address-en-container">
                    @forelse($addressEn as $item)
                    <div class="input-group mb-2">
                        <input type="text" name="address_en[]" class="form-control" value="{{ $item }}">
                        <button type="button" class="btn btn-danger" onclick="removeInput(this)">×</button>
                    </div>
                    @empty
                    <div class="input-group mb-2">
                        <input type="text" name="address_en[]" class="form-control">
                        <button type="button" class="btn btn-danger" onclick="removeInput(this)">×</button>
                    </div>
                    @endforelse
                </div>
                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="addDynamicInput('address-en-container', 'address_en[]')">
                    + {{ __('messages.add_address_en') }}
                </button>
                @error('address_en') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>

            {{-- Phone Numbers --}}
            <div class="mb-3">
                <label class="form-label">{{ __('messages.phone_numbers') }}</label>
                <div id="phones-container">
                    @forelse($phones as $phone)
                    <div class="input-group mb-2">
                        <input type="text" name="phones[]" class="form-control" value="{{ $phone }}">
                        <button type="button" class="btn btn-danger" onclick="removeInput(this)">×</button>
                    </div>
                    @empty
                    <div class="input-group mb-2">
                        <input type="text" name="phones[]" class="form-control">
                        <button type="button" class="btn btn-danger" onclick="removeInput(this)">×</button>
                    </div>
                    @endforelse
                </div>
                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="addDynamicInput('phones-container', 'phones[]')">
                    + {{ __('messages.add_phone') }}
                </button>
                @error('phones') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>

            {{-- Web Links --}}
            <div class="mb-3">
                <label class="form-label">{{ __('messages.web_links') }}</label>
                <div id="web-links-container">
                    @forelse($webLinks as $item)
                    <div class="input-group mb-2">
                        <input type="url" name="web_link[]" class="form-control" value="{{ $item }}">
                        <button type="button" class="btn btn-danger" onclick="removeInput(this)">×</button>
                    </div>
                    @empty
                    <div class="input-group mb-2">
                        <input type="url" name="web_link[]" class="form-control">
                        <button type="button" class="btn btn-danger" onclick="removeInput(this)">×</button>
                    </div>
                    @endforelse
                </div>
                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="addDynamicInput('web-links-container', 'web_link[]')">
                    + {{ __('messages.add_web_link') }}
                </button>
                @error('web_link') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>

            {{-- Email --}}
            <div class="mb-3">
                <label class="form-label">{{ __('messages.email') }}</label>
                <input type="email" name="email" class="form-control" value="{{ old('email', optional($data)->email) }}">
                @error('email') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>

            {{-- Working Hours AR --}}
            <div class="mb-3">
                <label class="form-label">{{ __('messages.work_hours_ar') }}</label>
                <input type="text" name="work_hours_ar" class="form-control" value="{{ old('work_hours_ar', optional($data)->work_hours_ar) }}">
                @error('work_hours_ar') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>

            {{-- Working Hours EN --}}
            <div class="mb-3">
                <label class="form-label">{{ __('messages.work_hours_en') }}</label>
                <input type="text" name="work_hours_en" class="form-control" value="{{ old('work_hours_en', optional($data)->work_hours_en) }}">
                @error('work_hours_en') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>

            {{-- Map Link --}}
            <div class="mb-3">
                <label class="form-label">{{ __('messages.map_link') }}</label>
                <input type="url" name="map_link" class="form-control" value="{{ old('map_link', optional($data)->map_link) }}">
                @error('map_link') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>

            {{-- WhatsApp Link --}}
            <div class="mb-3">
                <label class="form-label">{{ __('messages.whatsapp_link') }}</label>
                <input type="url" name="whatsapp_link" class="form-control" value="{{ old('whatsapp_link', optional($data)->whatsapp_link) }}">
                @error('whatsapp_link') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>

            <button type="submit" class="btn btn-primary">{{ __('messages.update') }}</button>
            <a href="{{ route('stay-in-touch.index') }}" class="btn btn-secondary">{{ __('messages.cancel') }}</a>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function removeInput(button) {
        const group = button.parentElement;
        group.remove();
    }

    function addDynamicInput(containerId, nameAttr) {
        const container = document.getElementById(containerId);
        const div = document.createElement('div');
        div.className = 'input-group mb-2';
        const input = document.createElement('input');
        input.type = nameAttr.includes('web_link') ? 'url' : 'text';
        input.name = nameAttr;
        input.className = 'form-control';
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'btn btn-danger';
        btn.textContent = '×';
        btn.onclick = () => removeInput(btn);
        div.appendChild(input);
        div.appendChild(btn);
        container.appendChild(div);
    }
</script>
@endpush
@endsection