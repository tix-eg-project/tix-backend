<?php

namespace App\Http\Requests\Web\Admin\Variant;

use Illuminate\Foundation\Http\FormRequest;

class VariantValueRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'variant_id'   => 'required|exists:product_variants,id',

            'name'         => 'required|array',
            'name.ar'      => 'required|string|min:2|max:255',
            'name.en'      => 'required|string|min:2|max:255',

            'meta'         => 'nullable|array',
        ];
    }

    /*************  ✨ Windsurf Command ⭐  *************/
    /**
     * Get the validation error messages that apply to the request.
     *
     * @return array<string, string>
     */
    /*******  37baaa0b-cb5f-4d96-ace1-ddbe5a4acede  *******/    public function messages(): array
    {
        return [
            'variant_id.required' => 'The variant field is required.',
            'variant_id.exists'   => 'The selected variant is invalid.',

            'name.required'       => 'The name field is required.',
            'name.array'          => 'The name must be a multi-language object.',
            'name.ar.required'    => 'The Arabic name field is required.',
            'name.ar.string'      => 'The Arabic name must be a string.',
            'name.ar.min'         => 'The Arabic name must be at least 2 characters.',
            'name.ar.max'         => 'The Arabic name may not be greater than 255 characters.',
            'name.en.required'    => 'The English name field is required.',
            'name.en.string'      => 'The English name must be a string.',
            'name.en.min'         => 'The English name must be at least 2 characters.',
            'name.en.max'         => 'The English name may not be greater than 255 characters.',

            'meta.array'          => 'Meta must be a valid JSON object.',
        ];
    }
}
