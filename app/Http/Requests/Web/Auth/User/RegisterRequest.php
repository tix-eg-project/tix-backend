<?php

namespace App\Http\Requests\Web\Auth\User;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email'                 => 'required|email|unique:users,email',
            'password'              => 'required|string|min:8|same:password_confirmation',
            'password_confirmation' => 'required|string|same:password',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required'                 => __('messages.email_required'),
            'email.email'                    => __('messages.email_invalid'),
            'email.unique'                   => __('messages.email_taken'),

            'password.required'              => __('messages.password_required'),
            'password.string'                => __('messages.password_string'),
            'password.min'                   => __('messages.password_min'),
            'password.same'                  => __('messages.password_confirmation_same'),

            'password_confirmation.required' => __('messages.password_confirmation_required'),
            'password_confirmation.string'   => __('messages.password_confirmation_string'),
            'password_confirmation.same'     => __('messages.password_confirmation_same'),
        ];
    }
}
