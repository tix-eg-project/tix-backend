<?php

namespace App\Http\Requests\Web\Admin\Order;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDeliveryDateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'actual_delivery_date' => ['required', 'date'],
        ];
    }
}
