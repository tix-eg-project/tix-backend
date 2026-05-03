<?php

namespace App\Http\Requests\Api\Auth\User;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules()
    {
        return [
            'email'    => 'required|email|exists:users,email',
            'password' => 'required|string',
        ];
    }

    public function messages()
    {
        return [
            'email.required'    => __('messages.email_required'),
            'email.email'       => __('messages.email_invalid'),
            'email.exists'      => __('messages.user_not_found'),
            'password.required' => __('messages.password_required'),
        ];
    }

}
