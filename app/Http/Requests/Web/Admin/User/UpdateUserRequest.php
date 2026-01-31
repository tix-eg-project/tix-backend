<?php

namespace App\Http\Requests\Web\Admin\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            // 'country_id' => 'required|exists:countries,id',
            'email' => "nullable|email|unique:users,email,{$this->user->id},id,deleted_at,NULL",
            'phone'           => [
                'required',
                'string',
                'regex:/^[0-9]+$/',
                "unique:users,phone,{$this->user->id},id,deleted_at,NULL",
            ],
            'password' => 'nullable|string|min:8',
            'service' => 'nullable|in:vendor,buyer',
            'national_number' => [
                'nullable',
                'string',
                'regex:/^[0-9]+$/',
                "unique:users,national_number,{$this->user->id},id,deleted_at,NULL"
            ],

            'category' => 'nullable|in:dealer,my',
            'role_id' => 'nullable|numeric|exists:roles,id',
            'image' => 'nullable|mimes:jpeg,png,jpg,gif,svg|max:102400'
        ];
    }
}
