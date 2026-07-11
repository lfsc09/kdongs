<?php

namespace App\Enums\Investment\FixedIncome;

enum AssetClass: string
{
    case PUBLIC = 'public';
    case PRIVATE = 'private';

    public function isPublic(): bool
    {
        return $this === self::PUBLIC;
    }

    public function isPrivate(): bool
    {
        return $this === self::PRIVATE;
    }

    public function label(): string
    {
        return match ($this) {
            self::PUBLIC => '(Public) Government',
            self::PRIVATE => '(Private) Corporate',
        };
    }
}
