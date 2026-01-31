<?php

namespace App\Http\Requests\Web\Admin\User;

use Illuminate\Foundation\Http\FormRequest;

class StoreAdminRequest extends FormRequest
{
    /*************  ✨ Windsurf Command ⭐  *************/
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    /*******  3110c420-bd1c-4f65-88cf-ceb7c58fb385  *******/
    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:admins,email'],
            'password' => ['required', 'min:6',],
            'role' => ['required', 'exists:roles,id'],

        ];
    }
    public function messages()
    {
        return [
            'name.required' => __('messages.validation.name_required'),
            'name.string' => __('messages.validation.name_string'),
            'name.max' => __('messages.validation.name_max'),
            'email.required' => __('messages.validation.email_required'),
            'email.email' => __('messages.validation.email_email'),
            'email.unique' => __('messages.validation.email_unique'),
            'password.required' => __('messages.validation.password_required'),
            'password.min' => __('messages.validation.password_min'),
            //'password.confirmed' => __('messages.validation.password_confirmed'),
            'role.required' => __('messages.validation.role_required'),
            'role.exists' => __('messages.validation.role_exists'),

        ];
    }

    public function authorize()
    {
        return true;
    }
}
