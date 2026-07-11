<?php

namespace App\Enums\Investment\FixedIncome;

enum IndexType: string
{
    case FIXED = 'fixed';
    // BRL
    case BRL_CDI = 'brl_cdi';
    case BRL_IPCA = 'brl_ipca';
    case BRL_SELIC = 'brl_selic';
    // USD
    case USD_EFFR = 'usd_effr';

    public function isFixed(): bool
    {
        return $this === self::FIXED;
    }

    public function isBrlCdi(): bool
    {
        return $this === self::BRL_CDI;
    }

    public function isBrlIpca(): bool
    {
        return $this === self::BRL_IPCA;
    }

    public function isBrlSelic(): bool
    {
        return $this === self::BRL_SELIC;
    }

    public function isUsdEffr(): bool
    {
        return $this === self::USD_EFFR;
    }

    public function label(): string
    {
        return match ($this) {
            self::FIXED => 'Fixed',
            self::BRL_CDI => '(BRL) CDI',
            self::BRL_IPCA => '(BRL) IPCA',
            self::BRL_SELIC => '(BRL) SELIC',
            self::USD_EFFR => '(USD) EFFR',
        };
    }
}
