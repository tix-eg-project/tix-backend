<?php

namespace App\Http\Requests\Web\Auth\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => 'nullable|string|max:255',
            'last_name'  => 'nullable|string|max:255',
            'country'    => 'nullable|string|max:255',
            'address'    => 'nullable|string|max:500',
            'phone'      => 'nullable|string|max:20',
            'image'     =>  'nullable',
        ];
    }
    public function messages(): array
    {
        return [
            'first_name.string' => __('messages.contact.first_name_string'),
            'last_name.string'  => __('messages.contact.last_name_string'),
            'country.string'    => __('messages.contact.country_string'),
            'address.string'    => __('messages.contact.address_string'),
            'phone.string'      => __('messages.contact.phone_string'),
                       
        ];
    }
}
