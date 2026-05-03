<?php

namespace App\Http\Requests\Web\Admin\Permission;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePermissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:permissions,name,' . $this->route('permission'),
            'guard_name' => 'required|in:admin',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => __('messages.validation.name_required'),
            'name.unique' => __('messages.validation.name_unique'),
            'guard_name.required' => __('messages.validation.guard_name_required'),
            'guard_name.in' => __('messages.validation.guard_name_invalid'),
        ];
    }
}
