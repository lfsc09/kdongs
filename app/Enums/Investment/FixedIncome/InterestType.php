<?php

namespace App\Enums\Investment\FixedIncome;

/**
 * OBS: Hybrid types (like IPCA+) should be converted to floating
 */
enum InterestType: string
{
    case FIXED = 'fixed';
    case FLOATING = 'floating'; // Or (post-fixed, adjustable, variable)

    public function isFixed(): bool
    {
        return $this === self::FIXED;
    }

    public function isFloating(): bool
    {
        return $this === self::FLOATING;
    }

    public function label(): string
    {
        return match ($this) {
            self::FIXED => 'Fixed',
            self::FLOATING => 'Floating',
        };
    }
}
