<?php

namespace App\Enums;

enum ReturnReasonEnum: int
{
    case ChangedMind = 1;
    case OrderMistake = 2;
    case FoundBetterPrice = 3;
    case ProductDefect = 4;
    case WrongItemSent = 5;
    case DamagedInTransit = 6;
    case WrongSize = 7;
    case Other = 8;

    public function key(): string
    {
        return match ($this) {
            self::ChangedMind => 'changed_mind',
            self::OrderMistake => 'order_mistake',
            self::FoundBetterPrice => 'found_better_price',
            self::ProductDefect => 'product_defect',
            self::WrongItemSent => 'wrong_item_sent',
            self::DamagedInTransit => 'damaged_in_transit',
            self::WrongSize => 'wrong_size',
            self::Other => 'other',
        };
    }

    public function label(): string
    {
        return __('returns.reason.' . $this->key());
    }

    public function restockingPercent(): int
    {
        return (int) (config('returns.restocking_percent_map.' . $this->value) ?? 0);
    }

    public static function options(): array
    {
        return array_map(
            fn(self $c) => ['value' => $c->value, 'label' => $c->label()],
            self::cases()
        );
    }
}
