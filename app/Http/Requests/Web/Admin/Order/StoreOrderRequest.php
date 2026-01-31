<?php

namespace App\Http\Requests\Web\Admin\Order;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            //  'expected_delivery_date' => 'nullable|date',

        ];
    }
}
