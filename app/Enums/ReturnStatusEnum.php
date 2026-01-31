<?php

namespace App\Enums;

enum ReturnStatusEnum: int
{
    case PendingReview     = 1;
    case UnderReturn       = 2;
    case ReceivedGood      = 3;
    case ReceivedDefective = 4;
    case Rejected          = 6;

    public function key(): string
    {
        return match ($this) {
            self::PendingReview     => 'pending_review',
            self::UnderReturn       => 'under_return',
            self::ReceivedGood      => 'received_good',
            self::ReceivedDefective => 'received_defective',
            self::Rejected          => 'rejected',
        };
    }

    public function label(): string
    {
        return __('messages' . $this->key());
    }

    public function canReceive(): bool
    {
        return $this === self::UnderReturn;
    }

    public function canRefund(): bool
    {
        return in_array($this, [self::ReceivedGood, self::ReceivedDefective], true);
    }
}
