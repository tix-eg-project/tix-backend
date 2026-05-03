<?php

namespace App\Http\Requests\Api\ProductReviews;

use Illuminate\Foundation\Http\FormRequest;

class ProductReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => 'required|exists:products,id',
            'comment'    => 'required|string|max:1000',
            'rating'     => 'required|integer|between:1,5',
            'image'      => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.required' => __('messages.review.product_required'),
            'product_id.exists'   => __('messages.review.product_not_found'),
            'comment.required'    => __('messages.review.comment_required'),
            'rating.required'     => __('messages.review.rating_required'),
            'rating.between'      => __('messages.review.rating_range'),
            'image.image'         => __('messages.review.image_invalid'),
            'image.max'           => __('messages.review.image_too_large'),
        ];
    }
}
