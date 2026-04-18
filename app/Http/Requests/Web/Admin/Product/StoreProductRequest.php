<?php

namespace App\Http\Requests\Web\Admin\Product;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name'        => 'required|array',
            'name.ar'     => 'required|string|max:255',
            'name.en'     => 'required|string|max:255',

            'short_description'      => 'nullable|array',
            'short_description.ar'   => 'nullable|string|max:500',
            'short_description.en'   => 'nullable|string|max:500',

            'long_description'       => 'nullable|array',
            'long_description.ar'    => 'nullable|string',
            'long_description.en'    => 'nullable|string',

            'price'         => 'required|numeric|min:0.01',
            'quantity'      => 'required|integer|min:0',
            'discount'      => 'nullable|numeric|min:0',
            'discount_type' => 'required_with:discount|in:1,2',

            'images'        => 'required|array|min:1',
            'images.*'      => 'required|image|mimes:jpeg,jpg,png,gif|max:2048',

            'category_id'    => 'required|exists:categories,id',
            'subcategory_id' => 'nullable|exists:subcategories,id',
            'brand_id'       => 'nullable|exists:brands,id',
            //'vendor_id'      => 'nullable|exists:vendors,id',
            'offer_id'       => 'nullable|exists:offers,id',

            'status'        => 'nullable',
            // في rules()
'features'              => 'nullable|array',
'features.*.key'        => 'nullable|string|max:255',
'features.*.value'      => 'nullable|string|max:255',

'faqs'                  => 'nullable|array',
'faqs.*.question'       => 'nullable|string|max:500',
'faqs.*.answer'         => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            // الاسم
            // في messages()
'features.array'            => __('messages.features_array'),
'features.*.key.string'     => __('messages.feature_key_string'),
'features.*.key.max'        => __('messages.feature_key_max'),
'features.*.value.string'   => __('messages.feature_value_string'),
'features.*.value.max'      => __('messages.feature_value_max'),

'faqs.array'                => __('messages.faqs_array'),
'faqs.*.question.string'    => __('messages.faq_question_string'),
'faqs.*.question.max'       => __('messages.faq_question_max'),
'faqs.*.answer.string'      => __('messages.faq_answer_string'),
            'name.required'     => __('messages.name_required'),
            'name.array'        => __('messages.name_array'),
            'name.ar.required'  => __('messages.name_ar_required'),
            'name.ar.string'    => __('messages.name_ar_string'),
            'name.ar.max'       => __('messages.name_ar_max'),
            'name.en.required'  => __('messages.name_en_required'),
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
            'price.required'    => __('messages.price_required'),
            'price.numeric'     => __('messages.price_numeric'),
            'price.min'         => __('messages.price_min'),

            'quantity.required' => __('messages.quantity_required'),
            'quantity.integer'  => __('messages.quantity_integer'),
            'quantity.min'      => __('messages.quantity_min'),

            'discount.numeric'  => __('messages.discount_numeric'),
            'discount.min'      => __('messages.discount_min'),
            'discount_type.required_with' => __('messages.discount_type_required'),
            'discount_type.in'  => __('messages.discount_type_in'),

            // الصور
            'images.required'   => __('messages.images_required'),
            'images.array'      => __('messages.images_array'),
            'images.min'        => __('messages.images_min'),
            'images.*.required' => __('messages.image_required'),
            'images.*.image'    => __('messages.image_invalid'),
            'images.*.mimes'    => __('messages.image_mimes'),
            'images.*.max'      => __('messages.image_max'),

            // العلاقات
            'category_id.required' => __('messages.category_required'),
            'category_id.exists'   => __('messages.category_exists'),
            'subcategory_id.exists' => __('messages.subcategory_exists'),
            'brand_id.exists'      => __('messages.brand_exists'),
            'vendor_id.exists'     => __('messages.vendor_exists'),
            'offer_id.exists'      => __('messages.offer_exists'),
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
