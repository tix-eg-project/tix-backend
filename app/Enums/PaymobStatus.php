<?php

namespace App\Enums;

enum PaymobStatus: int
{
    case Pending = 1;
    case Paid = 2;
    case Failed = 3;

    public static function availableTypes(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function getNameById(int $id): string
    {
        return match ($id) {
            self::Pending->value => 'Pending',
            self::Paid->value => 'Paid',
            self::Failed->value => 'Failed',
            default => 'UNKNOWN'
        };
    }

    public static function alphaTypes(): array
    {
        return [
            self::Pending->value => 'Pending',
            self::Paid->value => 'Paid',
            self::Failed->value => 'Failed',
        ];
    }
}
