<?php

namespace App\Http\Requests\Web\Admin\City;

use Illuminate\Foundation\Http\FormRequest;

class CityRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'array|between:2,2',
            'name.ar' => 'required|string|min:2|max:255',
            'name.en' => 'required|string|min:2|max:255',
            'country_id' => 'required|exists:countries,id',
        ];
    }
}
