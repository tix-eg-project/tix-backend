<?php

namespace App\Http\Requests\Web\Admin\Return;

use App\Enums\ReturnStatusEnum;
use App\Models\ReturnRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateReturnRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $allowed = [
            ReturnStatusEnum::UnderReturn->value,
            ReturnStatusEnum::ReceivedGood->value,
            ReturnStatusEnum::ReceivedDefective->value,
            ReturnStatusEnum::Rejected->value,
        ];

        return [
            // الحالة الجديدة اختيارية (لو هتعدّل تواريخ/شحن فقط)
            'new_status'          => ['nullable', 'integer', Rule::in($allowed)],

            // التواريخ كلها اختيارية
            'approved_at'         => ['nullable', 'date'],
            'received_at'         => ['nullable', 'date'],
            'refunded_at'         => ['nullable', 'date'],

            // خصم/استرداد الشحن + رقم محفظة + ملاحظة
            'refund_shipping'     => ['nullable', 'numeric', 'min:0'],
            'payout_wallet_phone' => ['nullable', 'string', 'max:30'],
            'restocking_percent'  => ['nullable', 'integer', 'min:0', 'max:100'],
            'admin_note'          => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'new_status.in'        => __('messages.illegal_transition'),
            'approved_at.date'     => __('validation.date'),
            'received_at.date'     => __('validation.date'),
            'refunded_at.date'     => __('validation.date'),
            'refund_shipping.min'  => __('validation.min.numeric'),
            'payout_wallet_phone.max' => __('validation.max.string'),
            'admin_note.max'       => __('validation.max.string'),
        ];
    }

    protected function prepareForValidation(): void
    {
        foreach (['new_status', 'restocking_percent'] as $k) {
            if ($this->filled($k)) $this->merge([$k => (int)$this->input($k)]);
        }
        if ($this->filled('refund_shipping')) {
            $this->merge(['refund_shipping' => (float)$this->input('refund_shipping')]);
        }
    }

    public function withValidator($validator)
    {
        $validator->after(function ($v) {
            /** @var ReturnRequest|null $rr */
            $rr = $this->route('return_request') ?? $this->route('return') ?? $this->route('id');
            if ($rr && !$rr instanceof ReturnRequest) {
                $rr = ReturnRequest::query()->find($rr);
            }
            if (!$rr) {
                $v->errors()->add('id', __('messages.not_found') ?? 'Return request not found');
                return;
            }

            // لو هيغير حالة، اتحقق من الانتقال المسموح
            if ($this->filled('new_status')) {
                $from = $rr->status instanceof ReturnStatusEnum ? $rr->status : ReturnStatusEnum::tryFrom((int)$rr->status);
                $to   = ReturnStatusEnum::tryFrom((int)$this->input('new_status'));

                if (!$from || !$to) {
                    $v->errors()->add('new_status', __('messages.invalid_decision_type'));
                    return;
                }

                $allowed = match ($from) {
                    ReturnStatusEnum::PendingReview     => [ReturnStatusEnum::UnderReturn, ReturnStatusEnum::Rejected],
                    ReturnStatusEnum::UnderReturn       => [ReturnStatusEnum::ReceivedGood, ReturnStatusEnum::ReceivedDefective, ReturnStatusEnum::Rejected],
                    ReturnStatusEnum::ReceivedGood      => [],
                    ReturnStatusEnum::ReceivedDefective => [],
                    ReturnStatusEnum::Rejected          => [],
                };

                if (!in_array($to, $allowed, true)) {
                    $v->errors()->add('new_status', __('messages.illegal_transition'));
                }
            }
        });
    }

    public function payload(): array
    {
        return $this->validated();
    }
}
