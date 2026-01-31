<?php

namespace App\Enums;

enum OrderStatusEnum: string
{
    case Pending = 'قيد التنفيذ';
    case Shipping = 'قيد التوصيل';
    case Delivered = 'تم التوصيل';
}
