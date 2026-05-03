<?php

namespace App\Http\Requests\Web\Admin\ShippingPrivacy;

use Illuminate\Foundation\Http\FormRequest;

class ShippingPolicyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'content_ar' => 'required|string',
            'content_en' => 'required|string',
        ];
    }

    public function attributes(): array
    {
        return [
            'content_ar' => __('messages.content_ar'),
            'content_en' => __('messages.content_en'),
        ];
    }
}
