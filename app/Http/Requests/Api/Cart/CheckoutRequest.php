<?php

namespace App\Http\Requests\Api\Cart;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'payment_method_id' => 'required|integer|exists:payment_methods,id',
        ];
    }

    public function messages(): array
    {
        return [
            'payment_method_id.required' => __('messages.cart.payment_method_id_required'),
            'payment_method_id.exists'   => __('messages.cart.payment_method_not_found'),
        ];
    }
}
