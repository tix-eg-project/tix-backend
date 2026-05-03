<?php

namespace App\Http\Requests\Api\Dashboard\SocialLink;

use Illuminate\Foundation\Http\FormRequest;

class StoreSocialLinkRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'platform' => 'sometimes|required|string', //in:facebook,Twitter,youtube,linkedin,behance,
            'url' => 'required',
        ];
    }
    public function messages(): array
    {
        return [
            'platform.in' => 'The platform must be one of facebook,Twitter,youtube,linkedin,behance',
            'required' => 'The :attribute field is required.',

            'url.required' => 'The url field is required.',
            'url.url' => 'The url must be a valid URL.',
            'url.max' => 'The url must not exceed 255 characters.',


        ];
    }
}
