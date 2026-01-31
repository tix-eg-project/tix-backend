<?php

namespace App\Http\Requests\Api\Cart;

use Illuminate\Foundation\Http\FormRequest;

class AddToCartRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // الاعتماد على middleware
    }

    public function rules(): array
    {
        return [
            'product_id' => 'required|integer|exists:products,id',
            'quantity'   => 'required|integer|min:1',
            'product_variant_item_id' => ['nullable', 'integer', 'exists:product_variant_items,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.required' => __('messages.cart.product_id_required'),
            'product_id.exists'   => __('messages.cart.product_not_found'),
            'quantity.required'   => __('messages.cart.quantity_required'),
            'quantity.min'        => __('messages.cart.quantity_min'),


            'product_variant_item_id.integer' => __('validation.integer'),
            'product_variant_item_id.exists'  => __('validation.exists'),
        ];
    }
}
