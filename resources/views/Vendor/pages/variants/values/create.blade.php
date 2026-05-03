{{-- resources/views/variants/values/create.blade.php --}}
@extends('Vendor.layout.app')
@section('title', __('messages.Add Value'))

@push('styles')
<style>
    .card-clean {
        border: 1px solid rgba(0, 0, 0, .08);
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, .06);
        background: #fff;
    }
</style>
@endpush

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="col-12 col-xl-9 mx-auto">
        <div class="card-clean p-4">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h5 class="mb-0">{{ __('messages.Add Value') }}</h5>
                <a href="{{ route('vendor.variants.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left"></i> {{ __('messages.back') ?? __('messages.cancel') }}
                </a>
            </div>
            <hr class="mt-2">

            @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
            @endif

            <form id="valueForm" method="POST" action="{{ route('vendor.variant-values.store', 0) }}">
                @csrf

                @php
                $currentVariantId = old('variant_id', isset($variant) ? $variant->id : null);
                $loc = app()->getLocale();
                @endphp

                <div class="mb-3">
                    <label class="form-label">{{ __('messages.Variant') }}</label>
                    <select id="variantSelect"
                        name="variant_id"
                        class="form-select @error('variant_id') is-invalid @enderror"
                        required>
                        <option value="">{{ __('messages.select') }}</option>
                        @foreach(($variants ?? []) as $v)
                        @php
                        $vName = method_exists($v, 'getTranslation')
                        ? $v->getTranslation('name', $loc, true)
                        : (is_array($v->name ?? null) ? ($v->name[$loc] ?? reset($v->name)) : ($v->name ?? ('#'.$v->id)));
                        @endphp
                        <option value="{{ $v->id }}" @selected((string)$currentVariantId===(string)$v->id)>
                            {{ $vName }} (ID: {{ $v->id }})
                        </option>
                        @endforeach
                    </select>
                    @error('variant_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>

                <div id="blocksContainer">
                    <div class="value-block card mb-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <strong class="block-title">{{ __('messages.Value') }} #1</strong>
                                <button type="button" class="btn btn-sm btn-danger" onclick="removeBlock(this)">×</button>
                            </div>

                            @foreach (LaravelLocalization::getSupportedLocales() as $localeCode => $locale)
                            <div class="mb-3">
                                <label class="form-label">{{ __('messages.Name') }} ({{ strtoupper($localeCode) }})</label>
                                <input type="text" name="name[{{ $localeCode }}]" class="form-control" value="">
                            </div>
                            @endforeach

                            <div class="mb-2">
                                <label class="form-label">Meta (JSON)</label>
                                <textarea name="meta" class="form-control" rows="2" placeholder='{"hex":"#ff0000","code":"XL"}'></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="button" class="btn btn-success mb-3" id="addBlockBtn">
                    + {{ __('messages.Add Value+') }}
                </button>

                <div class="mt-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary" id="submitBtn">{{ __('messages.save') }}</button>
                    <a href="{{ $currentVariantId ? route('vendor.variant-values.index', $currentVariantId) : route('vendor.variants.index') }}"
                        class="btn btn-secondary">{{ __('messages.cancel') }}</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    (function() {
        const form = document.getElementById('valueForm');
        const select = document.getElementById('variantSelect');
        const addBtn = document.getElementById('addBlockBtn');
        const blocksContainer = document.getElementById('blocksContainer');
        const submitBtn = document.getElementById('submitBtn');

        function updateFormAction() {
            const id = select.value || '0';
            const base = @json(route('vendor.variant-values.store', 0));
            const newAction = base.replace(/\/0(\/)?$/, '/' + id + (base.endsWith('/0/') ? '/' : ''));
            form.setAttribute('action', newAction);
        }

        if (select) {
            select.addEventListener('change', updateFormAction);
            updateFormAction();
        }

        addBtn.addEventListener('click', function() {
            const idx = blocksContainer.querySelectorAll('.value-block').length;
            const block = document.createElement('div');
            block.className = 'value-block card mb-3';
            block.innerHTML = `
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <strong class="block-title">{{ __('messages.Value') }} #${idx + 1}</strong>
          <button type="button" class="btn btn-sm btn-danger" onclick="removeBlock(this)">×</button>
        </div>
        @foreach (LaravelLocalization::getSupportedLocales() as $localeCode => $locale)
          <div class="mb-3">
            <label class="form-label">{{ __('messages.Name') }} ({{ strtoupper($localeCode) }})</label>
            <input type="text" name="name[{{ $localeCode }}]" class="form-control" value="">
          </div>
        @endforeach
        <div class="mb-2">
          <label class="form-label">Meta (JSON)</label>
          <textarea name="meta" class="form-control" rows="2" placeholder='{"hex":"#ff0000","code":"XL"}'></textarea>
        </div>
      </div>`;
            blocksContainer.appendChild(block);
            renumberBlocks();
        });

        window.removeBlock = function(btn) {
            const block = btn.closest('.value-block');
            if (blocksContainer.querySelectorAll('.value-block').length > 1) {
                block.remove();
                renumberBlocks();
            }
        };

        function renumberBlocks() {
            blocksContainer.querySelectorAll('.value-block .block-title').forEach((el, i) => {
                el.textContent = `{{ __('messages.Value') }} #${i + 1}`;
            });
        }

        form.addEventListener('submit', async function(e) {
            const blocks = blocksContainer.querySelectorAll('.value-block');
            if (blocks.length <= 1) return;

            e.preventDefault();

            if (!select.value) {
                alert(@json(__('messages.variant_id_required')));
                return;
            }

            submitBtn.disabled = true;
            addBtn.disabled = true;

            const action = form.getAttribute('action');
            const token = form.querySelector('input[name="_token"]').value;

            for (const block of blocks) {
                const fd = new FormData();
                fd.append('_token', token);
                fd.append('variant_id', select.value);

                const nameInputs = block.querySelectorAll('input[name^="name["]');
                const metaInput = block.querySelector('textarea[name="meta"]');

                nameInputs.forEach(inp => fd.append(inp.name, inp.value));
                if (metaInput && metaInput.value.trim() !== '') {
                    fd.append('meta', metaInput.value.trim());
                }

                const res = await fetch(action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: fd
                });

                if (!res.ok) {
                    let msg = 'Request failed';
                    try {
                        const data = await res.json();
                        if (data && data.message) msg = data.message;
                    } catch (_) {}
                    alert(msg);
                    submitBtn.disabled = false;
                    addBtn.disabled = false;
                    return;
                }
            }

            // ✅ بعد النجاح كله: رجوع لمسار الفندور الصحيح
            const to = @json(route('vendor.variant-values.index', 0)).replace(/\/0(\/)?$/, '/' + select.value);
            window.location.href = to;
        });
    })();
</script>
@endpush