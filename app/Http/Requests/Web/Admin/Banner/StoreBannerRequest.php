<?php

namespace App\Http\Requests\Web\Admin\Banner;

use Illuminate\Foundation\Http\FormRequest;

class StoreBannerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'array|between:2,2',
            'title.ar' => 'required|string|between:2,255',
            'title.en' => 'required|string|between:2,255',
            'description' => 'array|between:2,2',
            'description.ar' => 'required|string|between:2,500',
            'description.en' => 'required|string|between:2,500',
            'vendor_id' => 'nullable|exists:vendors,id',
            'image' => 'required|image|mimes:jpeg,png,jpg,webp'
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'       => __('messages.title_required'),
            'title.string'         => __('messages.title_string'),
            'title.between'        => __('messages.title_between'),

            'title.ar.required'       => __('messages.title_required'),
            'title.ar.string'         => __('messages.title_string'),
            'title.ar.between'        => __('messages.title_between'),

            'title.en.required'       => __('messages.title_required'),
            'title.en.string'         => __('messages.title_string'),
            'title.en.between'        => __('messages.title_between'),


            'description.required' => __('messages.description_required'),
            'description.string'   => __('messages.description_string'),
            'description.between'  => __('messages.description_between'),

            'description.ar.required' => __('messages.description_required'),
            'description.ar.string'   => __('messages.description_string'),
            'description.ar.between'  => __('messages.description_between'),

            'description.en.required' => __('messages.description_required'),
            'description.en.string'   => __('messages.description_string'),
            'description.en.between'  => __('messages.description_between'),

            'image.required'       => __('messages.image_required'),
            'image.image'          => __('messages.image_image'),
            'image.mimes'          => __('messages.image_mimes'),
            'image.max'            => __('messages.image_max'),
        ];
    }
}
