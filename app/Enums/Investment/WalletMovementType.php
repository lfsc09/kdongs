<?php

namespace App\Enums\Investments;

enum WalletMovementType: string
{
    case DEPOSIT = 'deposit';
    case WITHDRAWAL = 'withdrawal';

    public function isDeposit(): bool
    {
        return $this === self::DEPOSIT;
    }

    public function isWithdrawal(): bool
    {
        return $this === self::WITHDRAWAL;
    }

    public function label(): string
    {
        return match ($this) {
            self::DEPOSIT => 'Deposit',
            self::WITHDRAWAL => 'Withdrawal',
        };
    }
}
