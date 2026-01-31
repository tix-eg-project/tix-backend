<?php

namespace App\Http\Requests\Web\Admin\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAdminRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:admins,email,' . $this->admin->id],
            'password' => ['nullable', 'string', 'min:6'],
            'role' => ['required', 'exists:roles,id'],

        ];
    }
    public function messages(): array
    {
        return [
            'name.required' => __('messages.validation.name_required'),
            'name.string' => __('messages.validation.name_string'),
            'name.max' => __('messages.validation.name_max'),
            'email.required' => __('messages.validation.email_required'),
            'email.email' => __('messages.validation.email_email'),
            'email.unique' => __('messages.validation.email_unique'),
            'password.min' => __('messages.validation.password_min'),
            'role.required' => __('messages.validation.role_required'),
            'role.exists' => __('messages.validation.role_exists'),
        ];
    }
}
