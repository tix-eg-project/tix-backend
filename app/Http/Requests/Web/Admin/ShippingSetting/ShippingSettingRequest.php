<?php
namespace App\Http\Requests\Api\Dashboard\ShippingSetting;

use Illuminate\Foundation\Http\FormRequest;

class ShippingSettingRequest extends FormRequest
{
    public function authorize(): bool {
        return true;
    }

    public function rules(): array {
        return [
            'price' => 'required|numeric|min:0',
        ];
    }
}