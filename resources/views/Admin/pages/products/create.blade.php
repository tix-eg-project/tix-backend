@extends('Admin.layout.app')
@section('title', __('messages.add_product'))

@section('content')
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">{{ __('messages.add_product') }}</h5>

            <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data">
                @csrf

                {{-- ====== Dynamic Translations (Name / Short / Long) ====== --}}
                @foreach (LaravelLocalization::getSupportedLocales() as $localeCode => $locale)
                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.name') }} ({{ strtoupper($localeCode) }})</label>
                        <input type="text" name="name[{{ $localeCode }}]"
                            class="form-control @error(" name.$localeCode") is-invalid @enderror"
                            value="{{ old("name.$localeCode") }}">
                        @error("name.$localeCode")
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.short_description') }}
                            ({{ strtoupper($localeCode) }})</label>
                        <textarea name="short_description[{{ $localeCode }}]"
                            class="form-control ckeditor-desc @error(" short_description.$localeCode") is-invalid @enderror" rows="3">{{ old("short_description.$localeCode") }}</textarea>
                        @error("short_description.$localeCode")
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ __('messages.long_description') }}
                            ({{ strtoupper($localeCode) }})</label>
                        <textarea name="long_description[{{ $localeCode }}]"
                            class="form-control ckeditor-desc @error(" long_description.$localeCode") is-invalid @enderror" rows="5">{{ old("long_description.$localeCode") }}</textarea>
                        @error("long_description.$localeCode")
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <hr class="my-4">
                @endforeach

                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">{{ __('messages.price') }}</label>
                        <input type="number" step="0.01" min="0.01" name="price"
                            class="form-control @error('price') is-invalid @enderror" value="{{ old('price') }}">
                        @error('price')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">{{ __('messages.quantity') }}</label>
                        <input type="number" min="0" name="quantity"
                            class="form-control @error('quantity') is-invalid @enderror" value="{{ old('quantity', 0) }}">
                        @error('quantity')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">{{ __('messages.discount') }}</label>
                        <input type="number" step="0.01" min="0" name="discount"
                            class="form-control @error('discount') is-invalid @enderror" value="{{ old('discount') }}">
                        @error('discount')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">{{ __('messages.discount_type') }}</label>
                        <select name="discount_type" class="form-select @error('discount_type') is-invalid @enderror">
                            <option value="">{{ __('messages.select') }}</option>
                            <option value="1" {{ old('discount_type') == '1' ? 'selected' : '' }}>
                                {{ __('messages.percent') }}</option>
                            <option value="2" {{ old('discount_type') == '2' ? 'selected' : '' }}>
                                {{ __('messages.fixed') }}</option>
                        </select>
                        @error('discount_type')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row g-3 mt-2">
                    <div class="col-md-4">
                        <label class="form-label">{{ __('messages.category') }}</label>
                        <select id="category_id" name="category_id"
                            class="form-select @error('category_id') is-invalid @enderror">
                            <option value="">{{ __('messages.select_category') ?? __('messages.select') }}</option>
                            @foreach ($categories ?? [] as $c)
                                <option value="{{ $c->id }}" {{ old('category_id') == $c->id ? 'selected' : '' }}>
                                    {{ $c->name_text ?? ($c->name ?? '#' . $c->id) }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror

                    </div>

                    <div class="col-md-4">
                        <label class="form-label">{{ __('messages.subcategory') }}</label>
                        <select id="subcategory_id" name="subcategory_id"
                            class="form-select @error('subcategory_id') is-invalid @enderror" disabled>
                            <option value="">{{ __('messages.select_subcategory') ?? __('messages.select') }}
                            </option>
                        </select>
                        @error('subcategory_id')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">{{ __('messages.brand') }}</label>
                        <select name="brand_id" class="form-select @error('brand_id') is-invalid @enderror">
                            <option value="">{{ __('messages.select_brand') ?? __('messages.select') }}</option>
                            @foreach ($brands ?? [] as $b)
                                <option value="{{ $b->id }}" {{ old('brand_id') == $b->id ? 'selected' : '' }}>
                                    {{ $b->name_text ?? ($b->name ?? '#' . $b->id) }}
                                </option>
                            @endforeach
                        </select>
                        @error('brand_id')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row g-3 mt-2">



                    <div class="col-md-4">
                        <label class="form-label">{{ __('messages.status') }}</label>
                        <select name="status" class="form-select @error('status') is-invalid @enderror">
                            <option value="1" {{ old('status', 1) == 1 ? 'selected' : '' }}>
                                {{ __('messages.active') }}</option>
                            <option value="2" {{ old('status') == 2 ? 'selected' : '' }}>
                                {{ __('messages.inactive') }}</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 mt-3">
                    <label class="form-label">{{ __('messages.images') }}</label>

                    <div id="image-container">
                        <div class="input-group mb-2">
                            <input type="file" name="images[]" class="form-control" accept=".jpg,.jpeg,.png,.gif,.webp">
                            <button type="button" class="btn btn-success" onclick="addImageInput()">+</button>
                        </div>
                    </div>

                    <small class="text-muted d-block mt-1">
                        {{ __('messages.image_hint') ?? 'Allowed: jpg, jpeg, png, gif, webp. Max 2MB each.' }}
                    </small>
                </div>

                <hr class="my-4">

                {{-- ====== المميزات الرئيسية (لكل لغة) ====== --}}
                <div class="mb-4">
                    <h5 class="mb-3">{{ __('messages.key_features') ?? 'Key features' }}</h5>
                    <p class="text-muted small">{{ __('messages.key_features_hint') ?? 'One bullet per line for each language.' }}</p>
                    @foreach (LaravelLocalization::getSupportedLocales() as $localeCode => $locale)
                        <div class="mb-3">
                            <label class="form-label fw-semibold">{{ __('messages.key_features') ?? 'Key features' }} ({{ strtoupper($localeCode) }})</label>
                            <div id="features-{{ $localeCode }}-wrap">
                                <div class="input-group mb-2 feature-row">
                                    <input type="text" name="features[{{ $localeCode }}][]" class="form-control" value="">
                                    <button type="button" class="btn btn-outline-danger" onclick="this.closest('.feature-row').remove()" title="×">×</button>
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="addFeatureRow('{{ $localeCode }}')">+ {{ __('messages.add_line') ?? 'Add line' }}</button>
                        </div>
                    @endforeach
                </div>

                <hr class="my-4">

                {{-- ====== الأسئلة الشائعة ====== --}}
                <div class="mb-4">
                    <h5 class="mb-3">{{ __('messages.faq') ?? 'FAQ' }}</h5>
                    <div id="faq-container">
                        <div class="border rounded p-3 mb-3 faq-block">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fw-semibold">{{ __('messages.faq') ?? 'FAQ' }} #1</span>
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('.faq-block').remove()">{{ __('messages.remove') ?? 'Remove' }}</button>
                            </div>
                            @foreach (LaravelLocalization::getSupportedLocales() as $localeCode => $locale)
                                <div class="mb-2"><label class="form-label small mb-1">{{ __('messages.question') ?? 'Question' }} ({{ strtoupper($localeCode) }})</label><input type="text" name="faqs[0][question][{{ $localeCode }}]" class="form-control"></div>
                                <div class="mb-3"><label class="form-label small mb-1">{{ __('messages.answer') ?? 'Answer' }} ({{ strtoupper($localeCode) }})</label><textarea name="faqs[0][answer][{{ $localeCode }}]" class="form-control" rows="2"></textarea></div>
                            @endforeach
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="addFaqBlock()">+ {{ __('messages.add_faq') ?? 'Add FAQ' }}</button>
                </div>

                <hr class="my-4">

                {{-- ====== المراجعات والتقييمات (Read Only) ====== --}}
                <div class="mb-4">
                    <h5 class="mb-3">{{ __('messages.reviews') ?? 'Reviews' }}</h5>
                    <div class="alert alert-info">
                        <i class="bx bx-info-circle"></i>
                        {{ __('messages.reviews_available_after_creation') ?? 'Reviews will be available after product creation.' }}
                    </div>
                </div>

                <hr class="my-4">

                {{-- ====== التعليقات (Read Only) ====== --}}
                <div class="mb-4">
                    <h5 class="mb-3">{{ __('messages.comments') ?? 'Comments' }}</h5>
                    <div class="alert alert-info">
                        <i class="bx bx-info-circle"></i>
                        {{ __('messages.comments_available_after_creation') ?? 'Comments will be available after product creation.' }}
                    </div>
                </div>

                <hr class="my-4">

                <button type="submit" class="btn btn-primary">{{ __('messages.save') }}</button>
                <a href="{{ route('products.index') }}" class="btn btn-secondary">{{ __('messages.cancel') }}</a>
            </form>
        </div>
    </div>


    <script>
        function addImageInput() {
            const container = document.getElementById('image-container');

            const div = document.createElement('div');
            div.classList.add('input-group', 'mb-2');

            const input = document.createElement('input');
            input.type = 'file';
            input.name = 'images[]';
            input.classList.add('form-control');
            input.accept = '.jpg,.jpeg,.png,.gif,.webp';

            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.classList.add('btn', 'btn-danger');
            removeBtn.innerText = '×';
            removeBtn.onclick = () => container.removeChild(div);

            div.appendChild(input);
            div.appendChild(removeBtn);
            container.appendChild(div);
        }
    </script>

    <script>
        const TIX_LOCALES = @json(array_keys(LaravelLocalization::getSupportedLocales()));
        let faqNextIndex = 1;

        function addFeatureRow(locale) {
            const wrap = document.getElementById('features-' + locale + '-wrap');
            if (!wrap) return;
            const div = document.createElement('div');
            div.className = 'input-group mb-2 feature-row';
            div.innerHTML = '<input type="text" class="form-control" name="features[' + locale + '][]" value="">' +
                '<button type="button" class="btn btn-outline-danger" onclick="this.closest(\'.feature-row\').remove()">×</button>';
            wrap.appendChild(div);
        }

        function addFaqBlock() {
            const container = document.getElementById('faq-container');
            if (!container) return;
            const i = faqNextIndex++;
            let html = '<div class="border rounded p-3 mb-3 faq-block">' +
                '<div class="d-flex justify-content-between align-items-center mb-2">' +
                '<span class="fw-semibold">{{ __('messages.faq') ?? 'FAQ' }} #' + (i + 1) + '</span>' +
                '<button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest(\'.faq-block\').remove()">{{ __('messages.remove') ?? 'Remove' }}</button>' +
                '</div>';
            TIX_LOCALES.forEach(function(localeCode) {
                const uc = String(localeCode).toUpperCase();
                html += '<div class="mb-2"><label class="form-label small mb-1">{{ __('messages.question') ?? 'Question' }} (' + uc + ')</label><input type="text" class="form-control" name="faqs[' + i + '][question][' + localeCode + ']"></div>';
                html += '<div class="mb-3"><label class="form-label small mb-1">{{ __('messages.answer') ?? 'Answer' }} (' + uc + ')</label><textarea class="form-control" rows="2" name="faqs[' + i + '][answer][' + localeCode + ']"></textarea></div>';
            });
            html += '</div>';
            container.insertAdjacentHTML('beforeend', html);
        }
    </script>

    @push('scripts')
        <script src="https://cdn.ckeditor.com/4.22.1/full/ckeditor.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const editors = document.querySelectorAll('.ckeditor-desc');
                editors.forEach(editor => {
                    CKEDITOR.replace(editor, {
                        language: 'ar',
                        removePlugins: 'exportpdf',
                    });
                });
            });
        </script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const $cat = $('#category_id');
                const $sub = $('#subcategory_id');

                function resetSub(msg = '') {
                    $sub.prop('disabled', true).empty()
                        .append(
                            `<option value="">${msg || '{{ __('messages.select_subcategory') ?? __('messages.select') }}'}</option>`
                            );
                }

                // عند تغيير القسم الرئيسي
                $cat.on('change', function() {
                    const categoryId = this.value;
                    resetSub('{{ __('messages.loading') ?? 'تحميل...' }}');

                    if (!categoryId) return;

                    $.ajax({
                        url: `/api/category/${categoryId}/subcategories`,
                        method: 'GET',
                        success: function(res) {
                            const items = (res && res.data) ? res.data : [];
                            $sub.empty();
                            if (items.length) {
                                $sub.append(
                                    '<option value="" disabled selected hidden>-- {{ __('messages.select_subcategory') }} --</option>'
                                    );
                                items.forEach(function(sub) {
                                    $sub.append(
                                        `<option value="${sub.id}">${sub.name}</option>`
                                        );
                                });
                                // لو كان فيه قيمة قديمة (بعد فاليوديشن فاشل مثلاً)
                                const oldSub =
                                    '{{ old('
                                                            subcategory_id ') }}';
                                if (oldSub) $sub.val(oldSub);
                                $sub.prop('disabled', false);
                            } else {
                                resetSub('{{ __('messages.no_subcategories') }}');
                            }
                        },
                        error: function() {
                            resetSub('{{ __('messages.load_failed') }}');
                        }
                    });
                });

                // لو رجعنا من فاليوديشن وكان فيه category_id مختار مسبقًا
                const oldCat = '{{ old('
                        category_id ') }}';
                if (oldCat) {
                    $cat.val(oldCat).trigger('change');
                } else {
                    resetSub();
                }
            });
        </script>
    @endpush

@endsection
