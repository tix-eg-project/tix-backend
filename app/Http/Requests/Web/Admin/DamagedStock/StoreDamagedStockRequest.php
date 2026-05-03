<?php

namespace App\Http\Requests\Admin\DamagedStock;

use Illuminate\Foundation\Http\FormRequest;

class StoreDamagedStockRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'return_request_id'       => ['nullable', 'integer', 'exists:return_requests,id'],
            'vendor_id'               => ['nullable', 'integer', 'exists:vendors,id'],
            'product_id'              => ['nullable', 'integer', 'exists:products,id'],
            'product_variant_item_id' => ['nullable', 'integer', 'exists:product_variant_items,id'],

            'quantity'                => ['required', 'integer', 'min:1'],

            'reason_code'             => ['nullable', 'integer', 'between:1,50'],
            'reason_text'             => ['nullable', 'string', 'max:1000'],

            'warehouse_location'      => ['nullable', 'string', 'max:120'],
        ];
    }
}
