<?php

namespace App\Enums;

enum AmountType
{
    const percent = 1;
    const fixed = 2;

    public static function getLabel($type)
    {
        $types = [
            self::percent => __('messages.percent'),
            self::fixed => __('messages.fixed'),
        ];
        return $types[$type] ?? 'Unknown';
    }

    public static function getAvailable()
    {
        return [
            self::percent,
            self::fixed,
        ];
    }
}
