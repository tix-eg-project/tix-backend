<?php

namespace App\Http\Requests\Web\Admin\Product;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            // الاسم (ترجمات) - اختياري
            'name'        => 'sometimes|array',
            'name.ar'     => 'sometimes|required_with:name|string|max:255',
            'name.en'     => 'sometimes|required_with:name|string|max:255',

            // وصف مختصر + كامل (اختياري)
            'short_description'      => 'sometimes|nullable|array',
            'short_description.ar'   => 'sometimes|nullable|string|max:500',
            'short_description.en'   => 'sometimes|nullable|string|max:500',

            'long_description'       => 'sometimes|nullable|array',
            'long_description.ar'    => 'sometimes|nullable|string',
            'long_description.en'    => 'sometimes|nullable|string',

            // السعر/الخصم/الكمية (اختياري)
            'price'         => 'sometimes|numeric|min:0.01',
            'quantity'      => 'sometimes|integer|min:0',
            'discount'       => ['nullable', 'numeric', 'min:0'],
            'discount_type'  => ['nullable', 'integer', 'in:1,2'],

            // الصور (اختياري) + دعم حذف/استبدال
            'images'        => 'sometimes|array',
            'images.*'      => 'image|mimes:jpeg,jpg,png,gif|max:2048',

            'remove_images'   => 'sometimes|array',
            'remove_images.*' => 'string',
            'replace_images'  => 'sometimes|boolean',

            // العلاقات
            'category_id'    => 'sometimes|exists:categories,id',
            'subcategory_id' => 'sometimes|nullable|exists:subcategories,id',
            'brand_id'       => 'sometimes|nullable|exists:brands,id',
            'vendor_id'      => 'sometimes|nullable|exists:vendors,id',
            'offer_id'       => 'sometimes|nullable|exists:offers,id',

            // الحالة
            'status'        => 'sometimes',

            // مميزات المنتج (لكل لغة قائمة أسطر)
            'features'           => 'sometimes|array',
            'features.*'         => 'sometimes|array',
            'features.*.*'       => 'nullable|string|max:500',

            // أسئلة شائعة (سؤال/جواب JSON لكل لغة)
            'faqs'               => 'sometimes|array',
            'faqs.*.question'    => 'sometimes|array',
            'faqs.*.question.ar' => 'nullable|string|max:2000',
            'faqs.*.question.en' => 'nullable|string|max:2000',
            'faqs.*.answer'      => 'sometimes|array',
            'faqs.*.answer.ar'   => 'nullable|string',
            'faqs.*.answer.en'   => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            // الاسم
            'name.array'        => __('messages.name_array'),
            'name.ar.required_with' => __('messages.name_ar_required'),
            'name.ar.string'    => __('messages.name_ar_string'),
            'name.ar.max'       => __('messages.name_ar_max'),
            'name.en.required_with' => __('messages.name_en_required'),
            'name.en.string'    => __('messages.name_en_string'),
            'name.en.max'       => __('messages.name_en_max'),

            // الوصف المختصر
            'short_description.array'   => __('messages.short_desc_array'),
            'short_description.ar.string' => __('messages.short_desc_ar_string'),
            'short_description.ar.max'    => __('messages.short_desc_ar_max'),
            'short_description.en.string' => __('messages.short_desc_en_string'),
            'short_description.en.max'    => __('messages.short_desc_en_max'),

            // الوصف الكامل
            'long_description.array'    => __('messages.long_desc_array'),
            'long_description.ar.string' => __('messages.long_desc_ar_string'),
            'long_description.en.string' => __('messages.long_desc_en_string'),

            // السعر/الخصم/الكمية
            'price.numeric'     => __('messages.price_numeric'),
            'price.min'         => __('messages.price_min'),
            'quantity.integer'  => __('messages.quantity_integer'),
            'quantity.min'      => __('messages.quantity_min'),

            'discount.numeric'  => __('messages.discount_numeric'),
            'discount.min'      => __('messages.discount_min'),
            'discount_type.in'  => __('messages.discount_type_in'),

            // الصور
            'images.array'      => __('messages.images_array'),
            'images.*.image'    => __('messages.image_invalid'),
            'images.*.mimes'    => __('messages.image_mimes'),
            'images.*.max'      => __('messages.image_max'),

            'remove_images.array'   => __('messages.remove_images_array'),
            'remove_images.*.string' => __('messages.remove_images_string'),
            'replace_images.boolean' => __('messages.replace_images_boolean'),

            // العلاقات
            'category_id.exists'    => __('messages.category_exists'),
            'subcategory_id.exists' => __('messages.subcategory_exists'),
            'brand_id.exists'       => __('messages.brand_exists'),
            'vendor_id.exists'      => __('messages.vendor_exists'),
            'offer_id.exists'       => __('messages.offer_exists'),
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
