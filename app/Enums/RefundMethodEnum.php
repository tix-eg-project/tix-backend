<?php

namespace App\Enums;

enum RefundMethodEnum: int
{
    case OriginalPayment = 1;
    case Wallet          = 2;
    case Cash            = 3;


    public function key(): string
    {
        return match ($this) {
            self::OriginalPayment => 'original',
            self::Wallet          => 'wallet',
            self::Cash            => 'cash',
        };
    }

    public function label(): string
    {
        return __('messages.refund_method.' . $this->key());
    }

    public static function options(): array
    {
        return array_map(
            fn(self $c) => ['value' => $c->value, 'label' => $c->label()],
            self::cases()
        );
    }
}
