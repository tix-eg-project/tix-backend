<?php

namespace App\Http\Requests;

use App\Helpers\ValidationRuleHelper;
use Illuminate\Foundation\Http\FormRequest;

class AdminProfileRequest extends FormRequest
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
        $userId = auth()->id();
        return [
            'name'     => ['required', 'string'],
            'email'    => ['required', 'email', 'unique:users,email,' . $userId],
            'phone'    => ['nullable', 'string'],
            'image'    => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg'],
            // ✳️ الباسورد اختياري — لو اتبعت فقط
            'password' => ['nullable', 'string', 'min:6'],
        ];
    }
}
