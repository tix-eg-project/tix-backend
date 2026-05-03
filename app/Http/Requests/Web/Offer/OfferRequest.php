<?php

namespace App\Http\Requests\Web\Offer;

use Illuminate\Foundation\Http\FormRequest;

class OfferRequest extends FormRequest
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
            'name' => 'array|size:2',
            'name.ar' => 'required|string|min:2|max:255',
            'name.en' => 'required|string|min:2|max:255',
            'amount_type' => 'required|in:1,2',
            'amount_value' => 'required|numeric|min:0',
            'is_active' => 'nullable|boolean',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'vendor_id'     => 'nullable|exists:vendors,id',
        ];
        if ($this->isMethod('POST')) {
            $rules['products']   = 'required|array|min:1';
        } else {
            $rules['products']   = 'sometimes|array';
        }
        $rules['products.*'] = 'integer|exists:products,id';

        return $rules;
    }
}
