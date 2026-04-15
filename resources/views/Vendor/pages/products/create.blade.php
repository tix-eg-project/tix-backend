@extends('Vendor.layout.app')
@section('title', __('messages.add_product'))

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
    <div class="row">
        <div class="col-12 col-xl-11 mx-auto">
            <div class="card-clean p-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="mb-0">{{ __('messages.add_product') }}</h5>
                    <a href="{{ route('vendor.products.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-left"></i> {{ __('messages.back') }}
                    </a>
                </div>
                <hr class="mt-2">

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $e)
                                <li>{{ $e }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('vendor.products.store') }}" enctype="multipart/form-data">
                    @csrf

                    {{-- ====== Dynamic Translations (Name / Short / Long) ====== --}}
                    @foreach (LaravelLocalization::getSupportedLocales() as $localeCode => $locale)
                        <div class="mb-3">
                            <label class="form-label">{{ __('messages.name') }} ({{ strtoupper($localeCode) }})</label>
                            <input type="text" name="name[{{ $localeCode }}]"
                                class="form-control @error('name.' . $localeCode) is-invalid @enderror"
                                value="{{ old('name.' . $localeCode) }}">
                            @error('name.' . $localeCode)
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">{{ __('messages.short_description') }}
                                ({{ strtoupper($localeCode) }})</label>
                            <textarea name="short_description[{{ $localeCode }}]"
                                class="form-control ckeditor-desc @error('short_description.' . $localeCode) is-invalid @enderror" rows="3">{{ old('short_description.' . $localeCode) }}</textarea>
                            @error('short_description.' . $localeCode)
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">{{ __('messages.long_description') }}
                                ({{ strtoupper($localeCode) }})</label>
                            <textarea name="long_description[{{ $localeCode }}]"
                                class="form-control ckeditor-desc @error('long_description.' . $localeCode) is-invalid @enderror" rows="5">{{ old('long_description.' . $localeCode) }}</textarea>
                            @error('long_description.' . $localeCode)
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
                                class="form-control @error('quantity') is-invalid @enderror"
                                value="{{ old('quantity', 0) }}">
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
                                <option value="1" @selected(old('discount_type') == '1')>{{ __('messages.percent') }}</option>
                                <option value="2" @selected(old('discount_type') == '2')>{{ __('messages.fixed') }}</option>
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
                                <option value="">{{ __('messages.select_category') ?? __('messages.select') }}
                                </option>
                                @foreach ($categories ?? [] as $c)
                                    <option value="{{ $c->id }}" @selected(old('category_id') == $c->id)>
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
                                    <option value="{{ $b->id }}" @selected(old('brand_id') == $b->id)>
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
                                <option value="1" @selected(old('status', 1) == 1)>{{ __('messages.active') }}</option>
                                <option value="2" @selected(old('status') == 2)>{{ __('messages.inactive') }}</option>
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
                                <input type="file" name="images[]" class="form-control"
                                    accept=".jpg,.jpeg,.png,.gif,.webp">
                                <button type="button" class="btn btn-success" onclick="addImageInput()">+</button>
                            </div>
                        </div>
                        <small class="text-muted d-block mt-1">
                            {{ __('messages.image_hint') ?? 'Allowed: jpg, jpeg, png, gif, webp. Max 2MB each.' }}
                        </small>
                    </div>

                    <hr class="my-4">

                    {{-- ====== المميزات الرئيسية ====== --}}
                    <div class="card mb-4">
                        <div class="card-header bg-light fw-bold">
                            {{ __('messages.features') ?? 'المميزات الرئيسية' }}
                        </div>
                        <div class="card-body" id="features-container">
                            <div class="row g-2 mb-2 feature-row">
                                <div class="col-md-5">
                                    <input type="text" name="features[0][key]" class="form-control"
                                        placeholder="{{ __('messages.feature_key') ?? 'مثال: الوزن' }}">
                                </div>
                                <div class="col-md-5">
                                    <input type="text" name="features[0][value]" class="form-control"
                                        placeholder="{{ __('messages.feature_value') ?? 'مثال: 1.5 كيلو' }}">
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-danger w-100"
                                        onclick="this.closest('.feature-row').remove()">×</button>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="button" class="btn btn-success btn-sm" onclick="addFeatureRow()">
                                + {{ __('messages.add_feature') ?? 'إضافة ميزة' }}
                            </button>
                        </div>
                    </div>

                    {{-- ====== الأسئلة الشائعة (FAQ) ====== --}}
                    <div class="card mb-4">
                        <div class="card-header bg-light fw-bold">
                            {{ __('messages.faqs') ?? 'الأسئلة الشائعة' }}
                        </div>
                        <div class="card-body" id="faqs-container">
                            <div class="faq-row border rounded p-3 mb-3 bg-light">
                                <div class="mb-2">
                                    <label
                                        class="form-label small text-muted">{{ __('messages.question') ?? 'السؤال' }}</label>
                                    <input type="text" name="faqs[0][question]" class="form-control"
                                        placeholder="{{ __('messages.faq_question_placeholder') ?? 'مثال: هل المنتج يدعم الشحن السريع؟' }}">
                                </div>
                                <div class="mb-2">
                                    <label
                                        class="form-label small text-muted">{{ __('messages.answer') ?? 'الجواب' }}</label>
                                    <textarea name="faqs[0][answer]" class="form-control" rows="2"
                                        placeholder="{{ __('messages.faq_answer_placeholder') ?? 'مثال: نعم، يدعم شحن 65 واط...' }}"></textarea>
                                </div>
                                <button type="button" class="btn btn-danger btn-sm"
                                    onclick="this.closest('.faq-row').remove()">
                                    {{ __('messages.remove') ?? 'حذف' }}
                                </button>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="button" class="btn btn-success btn-sm" onclick="addFaqRow()">
                                + {{ __('messages.add_faq') ?? 'إضافة سؤال' }}
                            </button>
                        </div>
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

                    <div class="mt-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary">{{ __('messages.save') }}</button>
                        <a href="{{ route('vendor.products.index') }}"
                            class="btn btn-secondary">{{ __('messages.cancel') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Add image input --}}
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
            removeBtn.textContent = '×';
            removeBtn.onclick = () => container.removeChild(div);

            div.appendChild(input);
            div.appendChild(removeBtn);
            container.appendChild(div);
        }

        // ====== Features ======
        let featureIndex = 1;

        function addFeatureRow() {
            const container = document.getElementById('features-container');
            const row = document.createElement('div');
            row.classList.add('row', 'g-2', 'mb-2', 'feature-row');
            row.innerHTML = `
            <div class="col-md-5">
                <input type="text" name="features[${featureIndex}][key]" class="form-control" placeholder="{{ __('messages.feature_key') ?? 'مثال: الوزن' }}">
            </div>
            <div class="col-md-5">
                <input type="text" name="features[${featureIndex}][value]" class="form-control" placeholder="{{ __('messages.feature_value') ?? 'مثال: 1.5 كيلو' }}">
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-danger w-100" onclick="this.closest('.feature-row').remove()">×</button>
            </div>`;
            container.appendChild(row);
            featureIndex++;
        }

        // ====== FAQs ======
        let faqIndex = 1;

        function addFaqRow() {
            const container = document.getElementById('faqs-container');
            const row = document.createElement('div');
            row.classList.add('faq-row', 'border', 'rounded', 'p-3', 'mb-3', 'bg-light');
            row.innerHTML =
                `
            <div class="mb-2"><label class="form-label small text-muted">{{ __('messages.question') ?? 'السؤال' }}</label><input type="text" name="faqs[${faqIndex}][question]" class="form-control" placeholder="{{ __('messages.faq_question_placeholder') ?? 'مثال: هل المنتج يدعم الشحن السريع؟' }}"></div>
            <div class="mb-2"><label class="form-label small text-muted">{{ __('messages.answer') ?? 'الجواب' }}</label><textarea name="faqs[${faqIndex}][answer]" class="form-control" rows="2" placeholder="{{ __('messages.faq_answer_placeholder') ?? 'مثال: نعم...' }}"></textarea></div>
            <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('.faq-row').remove()">{{ __('messages.remove') ?? 'حذف' }}</button>`;
            container.appendChild(row);
            faqIndex++;
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
                        .append(`<option value="">${msg || @json(__('messages.select_subcategory') ?? __('messages.select'))}</option>`);
                }

                function loadSubs(categoryId, preselectId = null) {
                    resetSub(@json(__('messages.loading') ?? '...'));
                    if (!categoryId) return;

                    $.ajax({
                        url: `/api/category/${categoryId}/subcategories`,
                        method: 'GET',
                        success: function(res) {
                            const items = (res && res.data) ? res.data : [];
                            $sub.empty();
                            if (items.length) {
                                $sub.append(
                                    `<option value="" disabled selected hidden>-- ${@json(__('messages.select_subcategory'))} --</option>`
                                    );
                                items.forEach(sc => $sub.append(
                                    `<option value="${sc.id}">${sc.name}</option>`));
                                if (preselectId) $sub.val(String(preselectId));
                                $sub.prop('disabled', false);
                            } else {
                                resetSub(@json(__('messages.no_subcategories')));
                            }
                        },
                        error: function() {
                            resetSub(@json(__('messages.load_failed')));
                        }
                    });
                }

                // old() values safely from Blade to JS
                const oldCat = @json(old('category_id'));
                const oldSub = @json(old('subcategory_id'));

                if (oldCat) {
                    $cat.val(String(oldCat));
                    loadSubs(oldCat, oldSub || null);
                } else {
                    resetSub();
                }

                $cat.on('change', function() {
                    loadSubs(this.value, null);
                });
            });
        </script>
    @endpush
@endsection
