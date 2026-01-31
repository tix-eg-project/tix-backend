<?php

namespace App\Http\Requests\Admin\DamagedStock;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDamagedStockRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'quantity'           => ['nullable', 'integer', 'min:1'],
            'reason_code'        => ['nullable', 'integer', 'between:1,50'],
            'reason_text'        => ['nullable', 'string', 'max:1000'],
            'warehouse_location' => ['nullable', 'string', 'max:120'],
        ];
    }
}
