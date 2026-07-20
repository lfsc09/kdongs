<?php

namespace App\Enums\Investment;

enum CurrencyCode: string
{
    case USD = 'USD';
    case EUR = 'EUR';
    case BRL = 'BRL';

    public function isUsd(): bool
    {
        return $this === self::USD;
    }

    public function isEur(): bool
    {
        return $this === self::EUR;
    }

    public function isBrl(): bool
    {
        return $this === self::BRL;
    }

    /**
     * Formats the given amount according to the currency code. The amount is expected to be in cents (e.g., 12345 for $123.45).
     *
     * @param  int  $amount  The amount in cents to format.
     * @param  bool  $withSymbol  Whether to include the currency symbol in the formatted string.
     * @param  string  $locale  The locale to use for formatting (e.g., 'en_US', 'pt_BR'). Defaults to 'en_US'.
     * @return string The formatted currency
     */
    public function format(int $amount, bool $withSymbol = true, string $locale = 'en_US'): string
    {
        return match ($locale) {
            'en_US' => ($withSymbol ? self::symbol() : '').number_format($amount / 100, 2),
            'pt_BR' => ($withSymbol ? self::symbol() : '').number_format($amount / 100, 2, ',', '.'),
            default => ($withSymbol ? self::symbol() : '').number_format($amount / 100, 2),
        };
    }

    /**
     * Get the currency symbol for the current currency code.
     *
     * @return string The currency symbol
     */
    public function symbol(): string
    {
        return match ($this) {
            self::USD => '$',
            self::EUR => '€',
            self::BRL => 'R$',
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::USD => 'US Dollar',
            self::EUR => 'Euro',
            self::BRL => 'Brazilian Real',
        };
    }
}
