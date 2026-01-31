<?php

namespace App\Http\Requests\Web\Auth\Vendor;

use Illuminate\Foundation\Http\FormRequest;

class UpdateVendorProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('vendor')->check();
    }

    public function rules(): array
    {
        return [
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'image'       => ['nullable', 'file', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:4096'],
            'password'    => ['nullable', 'string', 'min:8'], // اختياري
        ];
    }

}
