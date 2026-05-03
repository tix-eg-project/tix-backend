<?php
namespace App\Http\Requests\Api\Auth\User;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules()
    {
        return [
            'email'    => 'required|email|exists:users,email',
            'New_password' => 'required|string|min:8|different:old_password',
            'New_password_confirmation' => 'required|string|same:New_password',
               
            
        ];
    }

    public function messages()
    {
        return [
            'email.required'        => __('messages.email_required'),
            'email.email'           => __('messages.email_invalid'),
            'email.exists'          => __('messages.user_not_found'),
            'New_password.required' => __('messages.password_required'),
            'New_password.string'   => __('messages.password_string'),
            'New_password.min'      => __('messages.password_min'),
            'New_password.different' => __('messages.password_same'),
            'New_password_confirmation.required' => __('messages.password_confirmation_required'),
            'New_password_confirmation.string'   => __('messages.password_confirmation_string'),
            'New_password_confirmation.same'     => __('messages.password_confirmation_same'),
            
        ];
    }

}
