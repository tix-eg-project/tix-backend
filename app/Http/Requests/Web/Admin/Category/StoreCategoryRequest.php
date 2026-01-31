<?php

namespace App\Http\Requests\Web\Admin\Category;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
{
    public function rules(): array
    {
        return [

            'name*' => '*|array|between:2,255',
            'name.ar' => 'required|string|max:255',
            'name.en' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,jpg,png,gif|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => __('messages.name_required'),
            'name.string' => __('messages.name_string'),
            'name.max' => __('messages.name_max'),

            'image.required' => __('messages.image_required'),
            'image.image' => __('messages.image_invalid'),
            'image.mimes' => __('messages.image_mimes'),
            'image.max' => __('messages.image_max'),
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
