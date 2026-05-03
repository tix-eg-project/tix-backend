<?php

namespace App\Http\Requests\Web\Admin\ShippingSetting;

use Illuminate\Foundation\Http\FormRequest;

class ShippingZoneRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // يمكن تعديلها حسب الصلاحيات لاحقًا
    }

    public function rules(): array
    {
        return [
            'name_ar'  => ['required', 'string', 'max:255'],
            'name_en'  => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'  => __('messages.name_required'),
            'name.string'    => __('messages.name_string'),
            'price.required' => __('messages.price_required'),
            'price.numeric'  => __('messages.price_numeric'),
            'price.min'      => __('messages.price_min'),
        ];
    }
}
