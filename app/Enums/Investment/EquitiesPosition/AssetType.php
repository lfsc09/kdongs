<?php

namespace App\Enums\Investment\EquitiesPosition;

enum AssetType: string
{
    case STOCK = 'stock';
    case FII = 'fii';
    case ETF = 'etf';
    case REIT = 'reit';

    public function isStock(): bool
    {
        return $this === self::STOCK;
    }

    public function isFii(): bool
    {
        return $this === self::FII;
    }

    public function isEtf(): bool
    {
        return $this === self::ETF;
    }

    public function isReit(): bool
    {
        return $this === self::REIT;
    }

    public function label(): string
    {
        return match ($this) {
            self::STOCK => 'Stock',
            self::FII => 'FII',
            self::ETF => 'ETF',
            self::REIT => 'REIT',
        };
    }
}
