<?php

namespace App\Http\Requests\Api\Cart;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserContactRequest extends FormRequest
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

            'phone' => ['required', 'string', 'min:8', 'max:15'],
            'address' => ['required', 'string', 'min:5', 'max:255'],
            'order_note' => ['required', 'string', 'min:5', 'max:255'],
        ];
    }
}
