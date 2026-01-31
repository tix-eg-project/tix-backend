@extends('Admin.layout.app')
@section('title', __('messages.edit_product'))

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <h4 class="fw-bold py-3 mb-4 text-center">{{ __('messages.edit_product') ?? 'Edit Product' }}</h4>

    <div class="card">
        <div class="card-body">

            <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- ====== الترجمات (اسم/وصف) ====== --}}
                @foreach (LaravelLocalization::getSupportedLocales() as $localeCode => $locale)
                @php
                // Helpers لصفحة التعديل فقط:
                // 1) getTransStrict => قيمة صريحة للّغة بدون fallback (value)
                // 2) getFallbackHint => تلميح باستخدام fallback (placeholder فقط)
                $getTransStrict = function($field) use ($localeCode, $product) {
                // old() أولاً
                $old = old("$field.$localeCode", null);
                if (!is_null($old)) return $old;

                // Spatie بدون fallback
                if (method_exists($product, 'getTranslation')) {
                $val = $product->getTranslation($field, $localeCode, false);
                if (!is_null($val) && $val !== '') return $val;
                }

                // Cast/Array صريح
                $val = $product->{$field};
                if (is_array($val) && array_key_exists($localeCode, $val)) {
                return $val[$localeCode] ?? '';
                }

                // JSON خام صريح
                if (method_exists($product, 'getRawOriginal')) {
                $raw = $product->getRawOriginal($field);
                if (is_string($raw) && $raw !== '') {
                $arr = json_decode($raw, true);
                if (is_array($arr) && array_key_exists($localeCode, $arr)) {
                return $arr[$localeCode] ?? '';
                }
                }
                }

                // لا توجد ترجمة لهذه اللغة
                return '';
                };

                $getFallbackHint = function($field) use ($localeCode, $product) {
                // استخدم Spatie بالـ fallback كـ تلميح فقط
                if (method_exists($product, 'getTranslation')) {
                $hint = $product->getTranslation($field, $localeCode, true);
                if (!is_null($hint) && $hint !== '') return $hint;
                // محاولة على fallback_locale صراحةً
                try {
                $fallbackLocale = config('app.fallback_locale');
                if ($fallbackLocale) {
                $hint2 = $product->getTranslation($field, $fallbackLocale, true);
                if (!is_null($hint2) && $hint2 !== '') return $hint2;
                }
                } catch (\Throwable $e) {}
                }
                // في غياب Spatie، حاول نص اللغة الحالية لو متاح
                return is_string($product->{$field} ?? null) ? $product->{$field} : '';
                };
                @endphp

                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label class="form-label">{{ __('messages.name') }} ({{ strtoupper($localeCode) }})</label>
                        <input type="text"
                            name="name[{{ $localeCode }}]"
                            class="form-control @error('name.'.$localeCode) is-invalid @enderror"
                            value="{{ $getTransStrict('name') }}"
                            placeholder="{{ $getTransStrict('name') === '' ? ($getFallbackHint('name') ?? '') : '' }}">
                        @error('name.'.$localeCode) <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label">{{ __('messages.short_description') }} ({{ strtoupper($localeCode) }})</label>
                        <textarea name="short_description[{{ $localeCode }}]"
                            class="form-control @error('short_description.'.$localeCode) is-invalid @enderror"
                            rows="3"
                            placeholder="{{ $getTransStrict('short_description') === '' ? ($getFallbackHint('short_description') ?? '') : '' }}">{{ $getTransStrict('short_description') }}</textarea>
                        @error('short_description.'.$localeCode) <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label">{{ __('messages.long_description') }} ({{ strtoupper($localeCode) }})</label>
                        <textarea name="long_description[{{ $localeCode }}]"
                            class="form-control @error('long_description.'.$localeCode) is-invalid @enderror"
                            rows="5"
                            placeholder="{{ $getTransStrict('long_description') === '' ? ($getFallbackHint('long_description') ?? '') : '' }}">{{ $getTransStrict('long_description') }}</textarea>
                        @error('long_description.'.$localeCode) <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>
                </div>

                <hr class="my-4">
                @endforeach

                {{-- ====== السعر/الكمية/الخصم/النوع/الحالة ====== --}}
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">{{ __('messages.price') }}</label>
                        <input type="number" step="0.01" min="0.01" name="price"
                            class="form-control @error('price') is-invalid @enderror"
                            value="{{ old('price', $product->price) }}">
                        @error('price') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">{{ __('messages.quantity') }}</label>
                        <input type="number" min="0" name="quantity"
                            class="form-control @error('quantity') is-invalid @enderror"
                            value="{{ old('quantity', $product->quantity) }}">
                        @error('quantity') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">{{ __('messages.discount') }}</label>
                        <input type="number" step="0.01" min="0" name="discount"
                            class="form-control @error('discount') is-invalid @enderror"
                            value="{{ old('discount', $product->discount) }}">
                        @error('discount') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">{{ __('messages.discount_type') }}</label>
                        <select name="discount_type" class="form-select @error('discount_type') is-invalid @enderror">
                            <option value="">{{ __('messages.select') }}</option>
                            <option value="1" @selected((int)old('discount_type', $product->discount_type) === 1)>{{ __('messages.percent') }}</option>
                            <option value="2" @selected((int)old('discount_type', $product->discount_type) === 2)>{{ __('messages.fixed') }}</option>
                        </select>
                        @error('discount_type') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row g-3 mt-1">
                    <div class="col-md-3">
                        <label class="form-label">{{ __('messages.status') }}</label>
                        <select name="status" class="form-select @error('status') is-invalid @enderror">
                            <option value="1" @selected((int)old('status', $product->status) === 1)>{{ __('messages.active') }}</option>
                            <option value="2" @selected((int)old('status', $product->status) === 2)>{{ __('messages.inactive') }}</option>
                        </select>
                        @error('status') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    {{-- القسم الرئيسي --}}
                    <div class="col-md-3">
                        <label class="form-label">{{ __('messages.category') }}</label>
                        <select id="category_id" name="category_id" class="form-select @error('category_id') is-invalid @enderror">
                            <option value="">{{ __('messages.select_category') ?? __('messages.select') }}</option>
                            @foreach(($categories ?? []) as $c)
                            <option value="{{ $c->id }}" @selected((int)old('category_id', $product->category_id) === (int)$c->id)>
                                {{ $c->name_text ?? $c->name ?? ('#'.$c->id) }}
                            </option>
                            @endforeach
                        </select>
                        @error('category_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    {{-- القسم الفرعي (ديناميكي) --}}
                    <div class="col-md-3">
                        <label class="form-label">{{ __('messages.subcategory') }}</label>
                        <select id="subcategory_id" name="subcategory_id" class="form-select @error('subcategory_id') is-invalid @enderror" disabled>
                            <option value="">{{ __('messages.select_subcategory') ?? __('messages.select') }}</option>
                            {{-- سيتم تعبئته عبر AJAX --}}
                        </select>
                        @error('subcategory_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">{{ __('messages.brand') }}</label>
                        <select name="brand_id" class="form-select @error('brand_id') is-invalid @enderror">
                            <option value="">{{ __('messages.select_brand') ?? __('messages.select') }}</option>
                            @foreach(($brands ?? []) as $b)
                            <option value="{{ $b->id }}" @selected((int)old('brand_id', $product->brand_id) === (int)$b->id)>
                                {{ $b->name_text ?? $b->name ?? ('#'.$b->id) }}
                            </option>
                            @endforeach
                        </select>
                        @error('brand_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>
                </div>

                {{-- صور جديدة --}}
                <div class="mb-3 mt-3">
                    <label class="form-label">{{ __('messages.add_images') ?? 'Add Images' }}</label>
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

                {{-- عرض الصور الحالية (تحت حقل إضافة صور فقط) --}}
                @php
                $currentUrls = [];
                if (isset($product->image_urls) && is_array($product->image_urls)) {
                $currentUrls = $product->image_urls;
                } else {
                $stored = (array)($product->images ?? []);
                foreach ($stored as $path) {
                if (is_string($path) && (str_starts_with($path, 'http://') || str_starts_with($path, 'https://'))) {
                $currentUrls[] = $path;
                } else {
                $currentUrls[] = asset('storage/'.ltrim($path ?? '', '/'));
                }
                }
                }
                @endphp

                @if(count($currentUrls))
                <div class="mb-3">
                    <label class="form-label d-block">{{ __('messages.current_images') ?? 'Current Images' }}</label>
                    <div class="d-flex flex-wrap align-items-center" style="gap:6px;">
                        @foreach ($currentUrls as $url)
                        <img src="{{ $url }}" width="72" height="72" style="object-fit:cover; border-radius:6px;" alt="">
                        @endforeach
                    </div>
                </div>
                @endif

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">{{ __('messages.save') ?? 'Save' }}</button>
                    <a href="{{ route('products.index') }}" class="btn btn-secondary">{{ __('messages.Back') ?? 'Back' }}</a>
                </div>
            </form>

        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const $cat = $('#category_id');
        const $sub = $('#subcategory_id');

        function resetSub(msg = '') {
            $sub.prop('disabled', true).empty()
                .append(`<option value="">${msg || '{{ __('messages.select_subcategory') ?? __('messages.select') }}'}</option>`);
        }

        function loadSubs(categoryId, preselectId = null) {
            resetSub('{{ __("messages.loading") ?? "تحميل..." }}');
            if (!categoryId) return;

            $.ajax({
                url: `/api/category/${categoryId}/subcategories`,
                method: 'GET',
                success: function(res) {
                    const items = (res && res.data) ? res.data : [];
                    $sub.empty();
                    if (items.length) {
                        $sub.append('<option value="" disabled selected hidden>-- {{ __("messages.select_subcategory") ?? "اختر القسم الفرعي" }} --</option>');
                        items.forEach(function(sc) {
                            $sub.append(`<option value="${sc.id}">${sc.name}</option>`);
                        });
                        if (preselectId) $sub.val(String(preselectId));
                        $sub.prop('disabled', false);
                    } else {
                        resetSub('{{ __("messages.no_subcategories") ?? "لا توجد أقسام فرعية" }}');
                    }
                },
                error: function() {
                    resetSub('{{ __("messages.load_failed") ?? "فشل في التحميل" }}');
                }
            });
        }

        $cat.on('change', function() {
            const catId = this.value;
            loadSubs(catId, null);
        });

        // تهيئة مبدئية: لو فيه قيمة قديمة/حالية
        const initialCat = '{{ old('
        category_id ', $product->category_id) }}';
        const initialSub = '{{ old('
        subcategory_id ', $product->subcategory_id) }}';
        if (initialCat) {
            $cat.val(String(initialCat));
            loadSubs(initialCat, initialSub || null);
        } else {
            resetSub();
        }
    });

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
@endpush
@endsection