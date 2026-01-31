<?php

namespace App\Http\Requests\Web\Admin\Subcategory;

use Illuminate\Foundation\Http\FormRequest;

class StoreSubcategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name*' => '*|array|between:2,255',
            'name.ar' => 'required|string|max:255',
            'name.en' => 'required|string|max:255',
            'description*' => '*|array|between:2,255',
            'description.ar' => 'required|string|max:255',
            'description.en' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The name field is required.',
            'name.string' => 'The name must be a string.',
            'name.between' => 'The name must be between :min and :max characters.',
            'name.ar.required' => 'The Arabic name field is required.',
            'name.ar.string' => 'The Arabic name must be a string.',
            'name.ar.between' => 'The Arabic name must be between :min and :max characters.',
            'name.en.required' => 'The English name field is required.',
            'name.en.string' => 'The English name must be a string.',
            'name.en.between' => 'The English name must be between :min and :max characters.',
            'description.required' => 'The description field is required.',
            'description.string' => 'The description must be a string.',
            'description.between' => 'The description must be between :min and :max characters.',
            'description.ar.required' => 'The Arabic description field is required.',
            'description.ar.string' => 'The Arabic description must be a string.',
            'description.ar.between' => 'The Arabic description must be between :min and :max characters.',
            'description.en.required' => 'The English description field is required.',
            'description.en.string' => 'The English description must be a string.',
            'description.en.between' => 'The English description must be between :min and :max characters.',
            'category_id.required' => 'The category field is required.',
            'category_id.exists' => 'The selected category is invalid.',
            'image.required' => 'The image is required.',
            'image.image' => 'The image must be an image.',
            'image.mimes' => 'The image must be a file of type: jpeg, png, jpg, gif, svg.',
            'image.max' => 'The image may not be greater than 2MB.',
        ];
    }
}
