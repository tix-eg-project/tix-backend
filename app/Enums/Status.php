<?php

namespace App\Enums;

enum Status
{
    const Active = 1;
    const Inactive = 2;


    public static function getLabel($type)
    {
        $types = [
            self::Active => __('messages.active'),
            self::Inactive => __('messages.inactive'),

        ];
        return $types[$type] ?? 'Unknown';
    }

    public static function getAvailable()
    {
        return [
            self::Active,
            self::Inactive,
        ];
    }
}
