<?php

namespace App\Enums\Investment;

enum CountryCode: string
{
    case US = 'US';
    case BR = 'BR';

    public function isUs(): bool
    {
        return $this === self::US;
    }

    public function isBr(): bool
    {
        return $this === self::BR;
    }

    public function label(): string
    {
        return match ($this) {
            self::US => 'United States',
            self::BR => 'Brazil',
        };
    }
}
