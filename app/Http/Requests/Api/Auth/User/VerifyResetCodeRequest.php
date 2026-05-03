<?php

namespace App\Http\Requests\Api\Auth\User;

use Illuminate\Foundation\Http\FormRequest;

class VerifyResetCodeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

   public function rules()
    {
        return [
            'email' => 'required|email|exists:users,email',
            'code'  => 'required|integer',
        ];
    }

    public function messages()
    {
        return [
            'email.required' => __('messages.email_required'),
            'email.email'    => __('messages.email_invalid'),
            'email.exists'   => __('messages.user_not_found'),
            'code.required'  => __('messages.code_required'),
            'code.integer'   => __('messages.code_invalid'),
        ];
    }


}
