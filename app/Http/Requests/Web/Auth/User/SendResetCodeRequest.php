<?php

namespace App\Http\Requests\Web\Auth\User;

use Illuminate\Foundation\Http\FormRequest;

class SendResetCodeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules()
    {
        return [
            'email' => 'required|email|exists:users,email',
        ];
    }

    public function messages()
    {
        return [
            'email.required' => __('messages.email_required'),
            'email.email'    => __('messages.email_invalid'),
            'email.exists'   => __('messages.email_not_registered'),
        ];
    }
}
