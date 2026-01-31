<?php

namespace App\Http\Requests\Web\Admin\User;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
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
        $user = auth()->user();
        return [
            'name' => 'required|string|max:255|regex:/^[a-zA-Z\s]+$/',
            'email' => "required|email|unique:users,email,{$user->id},id,deleted_at,NULL",
            'phone' => "required|numeric|unique:users,phone,{$user->id},id,deleted_at,NULL",
            'password' => 'nullable|min:8',
            'image' => 'nullable|mimes:jpeg,png,jpg,gif,svg|max:102400'
        ];
    }
}
