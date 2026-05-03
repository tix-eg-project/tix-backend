{{-- resources/views/variants/values/create.blade.php --}}
@extends('Admin.layout.app')

@section('title', __('messages.Add Value'))
@section('page_title', __('messages.Add Value'))

@section('content')
<div class="container-xxl container-p-y">

    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
        <h4 class="mb-0">{{ __('messages.Add Value') }}</h4>
        <a href="{{ route('variants.index') }}" class="btn btn-outline-secondary">
            <i class="bx bx-left-arrow-alt"></i> {{ __('messages.back') ?? __('messages.Back') }}
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <h5 class="card-title text-center mb-3">{{ __('messages.Add Value') }}</h5>

            @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
            @endif

            @php
            $currentVariantId = old('variant_id', isset($variant) ? $variant->id : null);
            $loc = app()->getLocale();
            @endphp

            <form id="valueForm"
                method="POST"
                action="{{ route('variant-values.store', 0) }}"
                data-action-template="{{ rtrim(route('variant-values.store', ':id'), '/') }}">
                @csrf

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

                {{-- Blocks container --}}
                <div id="blocksContainer">
                    <div class="value-block card mb-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <strong class="block-title">{{ __('messages.Value') }} #1</strong>
                                <button type="button" class="btn btn-sm btn-danger" onclick="removeBlock(this)">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
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
                    <i class="fa-solid fa-plus"></i> {{ __('messages.Add Value+') }}
                </button>

                <div class="mt-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i class="bx bx-save"></i> {{ __('messages.save') }}
                    </button>

                    <a href="{{ $currentVariantId ? route('variant-values.index', $currentVariantId) : route('variants.index') }}"
                        class="btn btn-secondary">
                        {{ __('messages.cancel') }}
                    </a>
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

        function buildActionUrl() {
            const template = form.getAttribute('data-action-template'); // .../variant-values/:id
            const id = (select.value || '0');
            return template.replace(':id', id);
        }

        function updateFormAction() {
            form.setAttribute('action', buildActionUrl());
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
          <button type="button" class="btn btn-sm btn-danger" onclick="removeBlock(this)">
            <i class="fa-solid fa-xmark"></i>
          </button>
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
                el.textContent = `{{ __('messages.Value') }} #${i+1}`;
            });
        }

        // submit: لو بلوك واحد → submit عادي (سيرفر). لو أكتر → نبعتهُم واحد واحد AJAX.
        form.addEventListener('submit', async function(e) {
            const blocks = blocksContainer.querySelectorAll('.value-block');
            if (blocks.length <= 1) return; // سيرفر عادي

            e.preventDefault();

            if (!select.value) {
                alert('{{ __("messages.variant_id_required") ?? "Variant is required" }}');
                return;
            }

            submitBtn.disabled = true;
            addBtn.disabled = true;

            const action = buildActionUrl();
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

            // الكل نجح → رجوع لقائمة قيم هذا الفاريانت
            const toBase = @json(route('variant-values.index', ':id'));
            window.location.href = toBase.replace(':id', select.value);
        });
    })();
</script>
@endpush