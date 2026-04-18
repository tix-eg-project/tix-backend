@extends('Admin.layout.app')
@section('title', __('messages.add_product'))

@section('content')
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">{{ __('messages.add_product') }}</h5>

            <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data">
                @csrf

                {{-- ====== بيانات ووصف المنتج (Name / Short / Long) ====== --}}
                <div class="mb-4">
                    <h5 class="mb-3 text-primary border-bottom pb-2">
                        <i class="bx bx-detail me-1"></i> {{ __('messages.product_details') ?? 'بيانات ووصف المنتج' }}
                    </h5>

                    <ul class="nav nav-tabs mb-3" id="productDescTabs" role="tablist">
                        @foreach (LaravelLocalization::getSupportedLocales() as $localeCode => $locale)
                            <li class="nav-item" role="presentation">
                                <button class="nav-link {{ $loop->first ? 'active' : '' }}"
                                    id="desc-{{ $localeCode }}-tab" data-bs-toggle="tab"
                                    data-bs-target="#desc-{{ $localeCode }}" type="button" role="tab"
                                    aria-controls="desc-{{ $localeCode }}"
                                    aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                                    <i class="bx bx-world me-1"></i> {{ $locale['native'] ?? strtoupper($localeCode) }}
                                </button>
                            </li>
                        @endforeach
                    </ul>

                    <div class="tab-content border rounded p-4 bg-light" id="productDescTabsContent">
                        @foreach (LaravelLocalization::getSupportedLocales() as $localeCode => $locale)
                            <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
                                id="desc-{{ $localeCode }}" role="tabpanel"
                                aria-labelledby="desc-{{ $localeCode }}-tab">
                                <div class="mb-3">
                                    <label class="form-label">{{ __('messages.name') }} <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="name[{{ $localeCode }}]"
                                        class="form-control @error("name.$localeCode") is-invalid @enderror"
                                        value="{{ old("name.$localeCode") }}"
                                        placeholder="{{ __('messages.name') }} ({{ strtoupper($localeCode) }})">
                                    @error("name.$localeCode")
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">{{ __('messages.short_description') }}</label>
                                    <textarea name="short_description[{{ $localeCode }}]"
                                        class="form-control ckeditor-desc @error("short_description.$localeCode") is-invalid @enderror" rows="3"
                                        placeholder="{{ __('messages.short_description') }} ({{ strtoupper($localeCode) }})">{{ old("short_description.$localeCode") }}</textarea>
                                    @error("short_description.$localeCode")
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">{{ __('messages.long_description') }}</label>
                                    <textarea name="long_description[{{ $localeCode }}]"
                                        class="form-control ckeditor-desc @error("long_description.$localeCode") is-invalid @enderror" rows="5">{{ old("long_description.$localeCode") }}</textarea>
                                    @error("long_description.$localeCode")
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <hr class="my-4">

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

                {{-- ====== المميزات الرئيسية (لكل لغة) ====== --}}
                <div class="mb-4">
                    <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
                        <h5 class="text-success mb-0">
                            <i class="bx bx-list-check me-1"></i> {{ __('messages.key_features') ?? 'المميزات الرئيسية' }}
                        </h5>
                    </div>
                    <p class="text-muted small mb-3">
                        <i class="bx bx-info-circle me-1"></i>
                        {{ __('messages.key_features_hint') ?? 'أضف ميزة واحدة في كل سطر لكل لغة للتركيز على أهم نقاط البيع للمنتج.' }}
                    </p>

                    <div class="row g-3">
                        @foreach (LaravelLocalization::getSupportedLocales() as $localeCode => $locale)
                            <div class="col-md-6">
                                <div class="card shadow-none border h-100">
                                    <div class="card-header bg-light py-2 border-bottom">
                                        <h6 class="mb-0 fw-bold">
                                            <i class="bx bx-world me-1 text-muted"></i>
                                            {{ __('messages.key_features') ?? 'المميزات' }}
                                            ({{ strtoupper($localeCode) }})
                                        </h6>
                                    </div>
                                    <div class="card-body p-3">
                                        <div id="features-{{ $localeCode }}-wrap">
                                            <div class="input-group mb-2 feature-row shadow-sm">
                                                <span class="input-group-text bg-white"><i
                                                        class="bx bx-check-circle text-success"></i></span>
                                                <input type="text" name="features[{{ $localeCode }}][]"
                                                    class="form-control border-start-0"
                                                    placeholder="{{ __('messages.feature_placeholder') ?? 'مثال: بطارية تدوم 24 ساعة...' }}">
                                                <button type="button" class="btn btn-outline-danger"
                                                    onclick="this.closest('.feature-row').remove()"
                                                    title="{{ __('messages.remove') ?? 'حذف' }}">
                                                    <i class="bx bx-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <button type="button"
                                            class="btn btn-sm btn-outline-success mt-2 w-100 rounded-pill"
                                            onclick="addFeatureRow('{{ $localeCode }}')">
                                            <i class="bx bx-plus"></i> {{ __('messages.add_line') ?? 'إضافة ميزة أخرى' }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <hr class="my-4">

                {{-- ====== الأسئلة الشائعة ====== --}}
                <div class="mb-4">
                    <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
                        <h5 class="text-info mb-0">
                            <i class="bx bx-conversation me-1"></i> {{ __('messages.faq') ?? 'الأسئلة والأجوبة (FAQ)' }}
                        </h5>
                        <button type="button" class="btn btn-sm btn-info text-white rounded-pill px-3"
                            onclick="addFaqBlock()">
                            <i class="bx bx-plus"></i> {{ __('messages.add_faq') ?? 'إضافة سؤال جديد' }}
                        </button>
                    </div>
                    <p class="text-muted small mb-3">
                        <i class="bx bx-info-circle me-1"></i> أضف الأسئلة الشائعة التي قد يطرحها العملاء حول هذا المنتج مع
                        إجاباتها.
                    </p>

                    <div id="faq-container">
                        <div class="card shadow-sm mb-4 faq-block border-info border-opacity-25">
                            <div
                                class="card-header bg-info bg-opacity-10 d-flex justify-content-between align-items-center py-2">
                                <h6 class="fw-bold mb-0 text-info">
                                    <i class="bx bx-help-circle me-1"></i> {{ __('messages.faq') ?? 'سؤال وجواب' }} <span
                                        class="faq-number">#1</span>
                                </h6>
                                <button type="button" class="btn btn-sm btn-outline-danger rounded-circle"
                                    style="width: 32px; height: 32px; padding: 0;"
                                    onclick="this.closest('.faq-block').remove()">
                                    <i class="bx bx-x"></i>
                                </button>
                            </div>
                            <div class="card-body p-3">
                                <div class="row g-4">
                                    @foreach (LaravelLocalization::getSupportedLocales() as $localeCode => $locale)
                                        <div class="col-md-6 border-end-md {{ $loop->last ? 'border-0' : '' }}">
                                            <h6 class="text-muted border-bottom pb-2 mb-3">
                                                <i class="bx bx-world me-1"></i> {{ strtoupper($localeCode) }}
                                            </h6>
                                            <div class="mb-3">
                                                <label
                                                    class="form-label text-dark fw-semibold mb-1">{{ __('messages.question') ?? 'السؤال' }}</label>
                                                <div class="input-group input-group-merge">
                                                    <span class="input-group-text"><i
                                                            class="bx bx-question-mark text-info"></i></span>
                                                    <input type="text" name="faqs[0][question][{{ $localeCode }}]"
                                                        class="form-control"
                                                        placeholder="{{ __('messages.question_placeholder') ?? 'مثال: هل المنتج مقاوم للماء؟' }}">
                                                </div>
                                            </div>

                                            <div>
                                                <label
                                                    class="form-label text-dark fw-semibold mb-1">{{ __('messages.answer') ?? 'الإجابة' }}</label>
                                                <div class="input-group input-group-merge">
                                                    <span class="input-group-text"><i
                                                            class="bx bx-message-dots text-success"></i></span>
                                                    <textarea name="faqs[0][answer][{{ $localeCode }}]" class="form-control" rows="2"
                                                        placeholder="{{ __('messages.answer_placeholder') ?? 'نعم، المنتج مقاوم للماء حتى عمق...' }}"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                {{-- ====== المراجعات والتعليقات (Read Only) ====== --}}
                <div class="mb-4">
                    <h5 class="mb-3 text-warning border-bottom pb-2">
                        <i class="bx bx-message-square-detail me-1"></i>
                        {{ __('messages.comments_and_reviews') ?? 'التعليقات والمراجعات' }}
                    </h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="card bg-warning bg-opacity-10 border-warning border-opacity-25 shadow-none h-100">
                                <div class="card-body text-center d-flex flex-column justify-content-center py-5">
                                    <div class="mb-3">
                                        <i class="bx bx-star fs-1 text-warning"></i>
                                        <i class="bx bxs-star fs-1 text-warning"></i>
                                        <i class="bx bx-star fs-1 text-warning"></i>
                                    </div>
                                    <h5 class="text-warning-emphasis fw-bold">
                                        {{ __('messages.reviews') ?? 'المراجعات والتقييمات' }}</h5>
                                    <p class="text-warning-emphasis small mb-0 mt-2 px-3">
                                        <i class="bx bx-lock-alt me-1"></i>
                                        {{ __('messages.reviews_available_after_creation') ?? 'التقييمات ستكون متاحة للعرض والإدارة بعد إنشاء المنتج بنجاح.' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div
                                class="card bg-secondary bg-opacity-10 border-secondary border-opacity-25 shadow-none h-100">
                                <div class="card-body text-center d-flex flex-column justify-content-center py-5">
                                    <div class="mb-3">
                                        <i class="bx bx-comment-detail fs-1 text-secondary"></i>
                                        <i class="bx bx-comment-dots fs-1 text-secondary ms-2"></i>
                                    </div>
                                    <h5 class="text-secondary-emphasis fw-bold">
                                        {{ __('messages.comments') ?? 'التعليقات والمناقشات' }}</h5>
                                    <p class="text-secondary-emphasis small mb-0 mt-2 px-3">
                                        <i class="bx bx-lock-alt me-1"></i>
                                        {{ __('messages.comments_available_after_creation') ?? 'التعليقات ستكون متاحة للعرض والإدارة بعد إنشاء المنتج بنجاح.' }}
                                    </p>
                                </div>
                            </div>
                        </div>
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
            div.className = 'input-group mb-2 feature-row shadow-sm';
            div.innerHTML = `
                <span class="input-group-text bg-white"><i class="bx bx-check-circle text-success"></i></span>
                <input type="text" class="form-control border-start-0" name="features[${locale}][]" placeholder="{{ __('messages.feature_placeholder') ?? 'أدخل الميزة هنا...' }}">
                <button type="button" class="btn btn-outline-danger" onclick="this.closest('.feature-row').remove()" title="{{ __('messages.remove') ?? 'حذف' }}">
                    <i class="bx bx-trash"></i>
                </button>
            `;
            wrap.appendChild(div);
        }

        function addFaqBlock() {
            const container = document.getElementById('faq-container');
            if (!container) return;
            const i = faqNextIndex++;
            let html = `
                <div class="card shadow-sm mb-4 faq-block border-info border-opacity-25">
                    <div class="card-header bg-info bg-opacity-10 d-flex justify-content-between align-items-center py-2">
                        <h6 class="fw-bold mb-0 text-info">
                            <i class="bx bx-help-circle me-1"></i> {{ __('messages.faq') ?? 'سؤال وجواب' }} <span class="faq-number">#${i + 1}</span>
                        </h6>
                        <button type="button" class="btn btn-sm btn-outline-danger rounded-circle" style="width: 32px; height: 32px; padding: 0;"
                            onclick="this.closest('.faq-block').remove(); updateFaqNumbers();">
                            <i class="bx bx-x"></i>
                        </button>
                    </div>
                    <div class="card-body p-3">
                        <div class="row g-4">
            `;

            TIX_LOCALES.forEach(function(localeCode, index) {
                const uc = String(localeCode).toUpperCase();
                const isLast = index === TIX_LOCALES.length - 1;
                const borderClass = isLast ? 'border-0' : 'border-end-md';
                html += `
                        <div class="col-md-6 ${borderClass}">
                            <h6 class="text-muted border-bottom pb-2 mb-3">
                                <i class="bx bx-world me-1"></i> ${uc}
                            </h6>
                            <div class="mb-3">
                                <label class="form-label text-dark fw-semibold mb-1">{{ __('messages.question') ?? 'السؤال' }}</label>
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="bx bx-question-mark text-info"></i></span>
                                    <input type="text" name="faqs[${i}][question][${localeCode}]"
                                        class="form-control"
                                        placeholder="{{ __('messages.question_placeholder') ?? 'مثال: هل المنتج مقاوم للماء؟' }}">
                                </div>
                            </div>
                            
                            <div>
                                <label class="form-label text-dark fw-semibold mb-1">{{ __('messages.answer') ?? 'الإجابة' }}</label>
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="bx bx-message-dots text-success"></i></span>
                                    <textarea name="faqs[${i}][answer][${localeCode}]" class="form-control" rows="2"
                                        placeholder="{{ __('messages.answer_placeholder') ?? 'نعم، المنتج مقاوم للماء حتى عمق...' }}"></textarea>
                                </div>
                            </div>
                        </div>
                `;
            });

            html += `
                        </div>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', html);
            updateFaqNumbers();
        }

        function updateFaqNumbers() {
            const blocks = document.querySelectorAll('.faq-block');
            blocks.forEach((block, index) => {
                const numSpan = block.querySelector('.faq-number');
                if (numSpan) numSpan.textContent = '#' + (index + 1);
            });
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
