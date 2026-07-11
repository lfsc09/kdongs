<?php

namespace App\Enums\Investment\EquitiesPosition;

enum TransactionType: string
{
    case BUY = 'buy';
    case SELL = 'sell';
    case TRANSFER_IN = 'transfer_in';
    case TRANSFER_OUT = 'transfer_out';
    case DIVIDEND = 'dividend';
    case SPLIT = 'split';
    case INPLIT = 'inplit';
    case BONUS_SHARES = 'bonus_shares';

    public function isBuy(): bool
    {
        return $this === self::BUY;
    }

    public function isSell(): bool
    {
        return $this === self::SELL;
    }

    public function isTransferIn(): bool
    {
        return $this === self::TRANSFER_IN;
    }

    public function isTransferOut(): bool
    {
        return $this === self::TRANSFER_OUT;
    }

    public function isDividend(): bool
    {
        return $this === self::DIVIDEND;
    }

    public function isSplit(): bool
    {
        return $this === self::SPLIT;
    }

    public function isInplit(): bool
    {
        return $this === self::INPLIT;
    }

    public function isBonusShares(): bool
    {
        return $this === self::BONUS_SHARES;
    }

    public function label(): string
    {
        return match ($this) {
            self::BUY => 'Buy',
            self::SELL => 'Sell',
            self::TRANSFER_IN => 'Transfer In',
            self::TRANSFER_OUT => 'Transfer Out',
            self::DIVIDEND => 'Dividend',
            self::SPLIT => 'Stock Split',
            self::INPLIT => 'Stock Inplit',
            self::BONUS_SHARES => 'Bonus Shares',
        };
    }
}
