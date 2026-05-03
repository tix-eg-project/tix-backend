<?php

namespace App\Enums;

enum TypeBusiness
{
    const person = 1;
    const company = 2;
    const institution = 3;

    public static function getLabel($type)
    {
        $types = [
            self::person => __('messages.person'),
            self::company => __('messages.company'),
            self::institution => __('messages.institution'),
        ];
        return $types[$type] ?? 'Unknown';
    }

    public static function getAvailable()
    {
        return [
            self::person,
            self::company,
            self::institution
        ];
    }
}
