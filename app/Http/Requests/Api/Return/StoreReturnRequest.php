<?php

namespace App\Http\Requests\Api\Return;

use App\Enums\ReturnReasonEnum;
use App\Models\Order;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreReturnRequest extends FormRequest
{
    public function authorize(): bool
    {
        $order = $this->getOrder();
        return $order && $this->user() && (int)$order->user_id === (int)$this->user()->id;
    }

    public function rules(): array
    {
        return [
            'order_item_id' => ['required', 'integer'],
            'quantity'      => ['required', 'integer', 'min:1'],

            'reason_code'   => ['required', 'integer', Rule::in(array_map(fn($c) => $c->value, ReturnReasonEnum::cases()))],
            'reason_text'   => ['nullable', 'string', 'max:2000'],

            'return_address' => ['nullable', 'array'],
            'return_address.name'     => ['nullable', 'string', 'max:190'],
            'return_address.phone'    => ['nullable', 'string', 'max:30'],
            'return_address.city'     => ['nullable', 'string', 'max:190'],
            'return_address.address1' => ['nullable', 'string', 'max:255'],
            'return_address.address2' => ['nullable', 'string', 'max:255'],
            'return_address.notes'    => ['nullable', 'string', 'max:1000'],

            'payout_wallet_phone' => ['nullable', 'string', 'max:30'],
            'refund_method'       => ['nullable', 'integer'],
        ];
    }

    public function messages(): array
    {
        return [
            'order_item_id.required' => __('messages.order_item_required'),
            'order_item_id.integer'  => __('validation.integer'),
            'quantity.required'      => __('messages.quantity_required'),
            'quantity.integer'       => __('validation.integer'),
            'quantity.min'           => __('messages.quantity_min'),
            'reason_code.required'   => __('messages.reason_required'),
            'reason_code.integer'    => __('validation.integer'),
            'reason_code.in'         => __('messages.reason_invalid'),
            'reason_text.string'     => __('validation.string'),
            'reason_text.max'        => __('validation.max.string'),
        ];
    }

    protected function prepareForValidation(): void
    {
        foreach (['order_item_id', 'quantity', 'reason_code'] as $k) {
            if ($this->has($k)) $this->merge([$k => (int)$this->input($k)]);
        }

        $ra = $this->input('return_address');

        if (is_string($ra)) {
            $decoded = json_decode($ra, true);

            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $this->merge(['return_address' => $decoded]);
            } elseif (trim($ra) !== '') {
                $this->merge(['return_address' => ['address1' => $ra]]);
            } else {
                $this->request->remove('return_address');
            }
        }
    }


    private function getOrder(): ?Order
    {
        $param = $this->route('order') ?? $this->route('id') ?? $this->route('order_id');
        if ($param instanceof Order) return $param;
        if (is_numeric($param)) return Order::query()->find((int)$param);
        return null;
    }

    public function payload(): array
    {
        return $this->validated();
    }
}
