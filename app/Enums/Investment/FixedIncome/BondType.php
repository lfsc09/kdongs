<?php

namespace App\Enums\Investment\FixedIncome;

enum BondType: string
{
    // BRL
    case BRL_CDB = 'brl_cdb';
    case BRL_LCA = 'brl_lca';
    case BRL_LCI = 'brl_lci';
    case BRL_DEBENTURE = 'brl_debenture';
    case BRL_TREASURY_SELIC = 'brl_treasury_selic';
    case BRL_TREASURY_IPCA = 'brl_treasury_ipca';
    case BRL_TREASURY_FIXED = 'brl_treasury_fixed';
    // USD
    case USD_CORPORATE_BOND = 'usd_corporate_bond';
    case USD_TREASURY_BILL = 'usd_treasury_bill';
    case USD_TREASURY_NOTE = 'usd_treasury_note';

    public function isBrlCdb(): bool
    {
        return $this === self::BRL_CDB;
    }

    public function isBrlLca(): bool
    {
        return $this === self::BRL_LCA;
    }

    public function isBrlLci(): bool
    {
        return $this === self::BRL_LCI;
    }

    public function isBrlDebenture(): bool
    {
        return $this === self::BRL_DEBENTURE;
    }

    public function isBrlTreasurySelic(): bool
    {
        return $this === self::BRL_TREASURY_SELIC;
    }

    public function isBrlTreasuryIpca(): bool
    {
        return $this === self::BRL_TREASURY_IPCA;
    }

    public function isBrlTreasuryFixed(): bool
    {
        return $this === self::BRL_TREASURY_FIXED;
    }

    public function isUsdCorporateBond(): bool
    {
        return $this === self::USD_CORPORATE_BOND;
    }

    public function isUsdTreasuryBill(): bool
    {
        return $this === self::USD_TREASURY_BILL;
    }

    public function isUsdTreasuryNote(): bool
    {
        return $this === self::USD_TREASURY_NOTE;
    }

    public function label(): string
    {
        return match ($this) {
            self::BRL_CDB => '(BRL) CDB',
            self::BRL_LCA => '(BRL) LCA',
            self::BRL_LCI => '(BRL) LCI',
            self::BRL_DEBENTURE => '(BRL) Debenture',
            self::BRL_TREASURY_SELIC => '(BRL) Treasury Selic',
            self::BRL_TREASURY_IPCA => '(BRL) Treasury IPCA',
            self::BRL_TREASURY_FIXED => '(BRL) Treasury Fixed',
            self::USD_CORPORATE_BOND => '(USD) Corporate Bond',
            self::USD_TREASURY_BILL => '(USD) Treasury Bill',
            self::USD_TREASURY_NOTE => '(USD) Treasury Note',
        };
    }
}
