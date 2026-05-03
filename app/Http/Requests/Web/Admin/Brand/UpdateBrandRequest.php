<?php

namespace App\Http\Requests\Web\Admin\Brand;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBrandRequest extends FormRequest
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
        ];
    }
    public function messages()
    {
        return [
            'name.required' => '__(messages.name_required)',
            'name.string' => '__(messages.name_string)',
            'name.max' => '__(messages.name_max)',
        ];
    }
}
