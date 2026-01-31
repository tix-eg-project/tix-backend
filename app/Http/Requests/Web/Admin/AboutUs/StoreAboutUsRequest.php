<?php

namespace App\Http\Requests\Api\Dashboard\AboutUs;

use Illuminate\Foundation\Http\FormRequest;

class StoreAboutUsRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title'               => ['nullable', 'array'],
            'title.ar'            => ['nullable', 'string', 'max:255'],
            'title.en'            => ['nullable', 'string', 'max:255'],

            'description'         => ['required', 'array'],
            'description.ar'      => ['required', 'string'],
            'description.en'      => ['required', 'string'],

            'image'               => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ];
    }

    public function messages(): array
    {
        return [
            'required' => __('validation.required'),
            'string'   => __('validation.string'),
            'image'    => __('validation.image'),
        ];
    }

    public function attributes(): array
    {
        return [
            // 'title_ar' => 'العنوان (عربي)',
            //'title_en' => 'العنوان (إنجليزي)',
            'description_ar' => 'الوصف (عربي)',
            'description_en' => 'الوصف (إنجليزي)',
            'image' => 'الصورة',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
