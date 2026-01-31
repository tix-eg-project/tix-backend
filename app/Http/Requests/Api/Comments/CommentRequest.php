<?php


namespace App\Http\Requests\Api\Comments;

use Illuminate\Foundation\Http\FormRequest;

class CommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'order_id' => 'required|exists:orders,id',
            'comment'    => 'required|string|max:1000',
            'rating'     => 'nullable|integer|between:1,5',
        ];
    }

    public function messages(): array
    {
        return [
            'order_id.required' => __('messages.comment.order_required'),
            'comment.required'    => __('messages.comment.comment_required'),
            'rating.between'      => __('messages.comment.rating_range'),
        ];
    }
}
