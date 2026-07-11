<?php

namespace App\Enums\Investment\FixedIncome;

enum TransactionType: string
{
    case BUY = 'buy';
    case SELL = 'sell';

    public function isBuy(): bool
    {
        return $this === self::BUY;
    }

    public function isSell(): bool
    {
        return $this === self::SELL;
    }

    public function label(): string
    {
        return match ($this) {
            self::BUY => 'Buy',
            self::SELL => 'Sell',
        };
    }
}
