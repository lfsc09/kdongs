<?php

namespace Database\Factories\Investment;

use App\Enums\Investments\CurrencyCode;
use App\Models\Investment\Wallet;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Wallet>
 */
class WalletFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word(),
            'view_currency' => $this->faker->randomElement(CurrencyCode::cases()),
            'fiscal_residence_currency' => $this->faker->randomElement(CurrencyCode::cases()),
        ];
    }

    /**
     * Indicate that the model should have a specific currency code.
     *
     * @param  CurrencyCode  $currencyCode  The currency code to set for the wallet.
     * @return static The factory instance with the specified currency code.
     */
    public function ofViewCurrency(CurrencyCode $currencyCode): static
    {
        return $this->state(fn (array $attributes) => [
            'view_currency' => $currencyCode,
        ]);
    }

    /**
     * Indicate that the model should have a specific fiscal residence currency code.
     *
     * @param  CurrencyCode  $currencyCode  The currency code to set for the wallet.
     * @return static The factory instance with the specified currency code.
     */
    public function ofFiscalResidenceCurrency(CurrencyCode $currencyCode): static
    {
        return $this->state(fn (array $attributes) => [
            'fiscal_residence_currency' => $currencyCode,
        ]);
    }
}
