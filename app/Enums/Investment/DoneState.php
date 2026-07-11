<?php

namespace App\Enums\Investment;

enum DoneState: string
{
    case ACTIVE = 'active';
    case DONE = 'done';
    case TRANSFERRED = 'transferred';

    public function isActive(): bool
    {
        return $this === self::ACTIVE;
    }

    public function isDone(): bool
    {
        return $this === self::DONE;
    }

    public function isTransferred(): bool
    {
        return $this === self::TRANSFERRED;
    }

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Active',
            self::DONE => 'Done',
            self::TRANSFERRED => 'Transferred',
        };
    }
}
