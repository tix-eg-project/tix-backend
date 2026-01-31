<?php

namespace App\Http\Requests\Web\Admin\Coupon;

use Illuminate\Foundation\Http\FormRequest;

class StoreCouponRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'code'           => 'required|string|unique:coupons,code',
            'discount_type'  => 'required|in:amount,percent',
            'discount_value' => 'required|numeric|min:0.01',
            'starts_at'      => 'required|date|before:ends_at',
            'ends_at'        => 'required|date|after:starts_at',
            'min_amount'     => 'nullable|numeric|min:0',
            'max_uses'       => 'nullable|integer|min:0',
            'is_active'      => 'nullable|boolean',
        ];
    }

    public function messages()
    {
        return [
            'code.required'           => __('messages.coupon_code_required'),
            'code.unique'             => __('messages.coupon_code_unique'),
            'discount_type.required'  => __('messages.discount_type_required'),
            'discount_type.in'        => __('messages.discount_type_invalid'),
            'discount_value.required' => __('messages.discount_value_required'),
            'discount_value.numeric'  => __('messages.discount_value_numeric'),
            'discount_value.min'      => __('messages.discount_value_min'),
            'starts_at.required'      => __('messages.starts_at_required'),
            'starts_at.date'          => __('messages.starts_at_date'),
            'starts_at.before'        => __('messages.starts_at_before'),
            'ends_at.required'        => __('messages.ends_at_required'),
            'ends_at.date'            => __('messages.ends_at_date'),
            'ends_at.after'           => __('messages.ends_at_after'),
            'min_amount.numeric'      => __('messages.min_amount_numeric'),
            'min_amount.min'          => __('messages.min_amount_min'),
        ];
    }
}
