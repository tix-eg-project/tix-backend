<?php

namespace App\Http\Requests\Web\Admin\Variant;

use Illuminate\Foundation\Http\FormRequest;

class ProductItemVariantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // المطلوب إلزامي
            'price' => ['required', 'numeric', 'min:0'],

            // [variant_id => value_id|null]
            'variant_values'   => ['nullable', 'array'],
            'variant_values.*' => ['nullable', 'integer', 'exists:product_variant_values,id'],
        ];
    }

    /**
     * تنظيف القيم قبل الفاليديشن:
     * نحول "" في الراديو إلى null عشان ما تتحسبش قيمة مختارة.
     */
    protected function prepareForValidation(): void
    {
        $vv = $this->input('variant_values', []);
        if (is_array($vv)) {
            $vv = array_map(fn($v) => $v === '' ? null : $v, $vv);
            $this->merge(['variant_values' => $vv]);
        }
    }

    public function messages(): array
    {
        return [
            'price.required'     => __('Please enter a price'),
            'price.numeric'      => __('Price must be a number'),
            'price.min'          => __('Price must be at least 0'),
            'variant_values.*.integer' => __('Invalid variant value'),
            'variant_values.*.exists'  => __('Variant value not found'),
        ];
    }
}
