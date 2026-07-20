<?php

namespace Database\Factories\Investment;

use App\Enums\Investment\CurrencyCode;
use App\Enums\Investment\WalletMovementType;
use App\Models\Investment\WalletMovement;
use BcMath\Number;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WalletMovement>
 */
class WalletMovementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        /** @var CurrencyCode $randomSourceCurrency */
        $randomSourceCurrency = $this->faker->randomElement(CurrencyCode::cases());
        /** @var CurrencyCode $randomTargetCurrency */
        $randomTargetCurrency = $this->faker->boolean(70) ? $randomSourceCurrency : $this->faker->randomElement(CurrencyCode::cases());

        [
            'randomSourceAmount' => $randomSourceAmount,
            'randomFxGrossRate' => $randomFxGrossRate,
            'randomFxFeePercentage' => $randomFxFeePercentage,
            'randomFxNetRate' => $randomFxNetRate,
            'randomTargetAmount' => $randomTargetAmount,
        ] = $this->generateConversionValues($randomSourceCurrency, $randomTargetCurrency);

        return [
            'type' => $this->faker->randomElement(WalletMovementType::cases()),
            'institution' => $this->faker->company(),
            'source_currency' => $randomSourceCurrency,
            'source_amount' => $randomSourceAmount,
            'fx_gross_rate' => $randomFxGrossRate,
            'fx_fee_percentage' => $randomFxFeePercentage,
            'fx_net_rate' => $randomFxNetRate,
            'target_currency' => $randomTargetCurrency,
            'target_amount' => $randomTargetAmount,
            'details' => $this->faker->sentence(),
        ];
    }

    /**
     * Indicate that the model should be a deposit movement.
     */
    public function asDeposit(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => WalletMovementType::DEPOSIT,
        ]);
    }

    /**
     * Indicate that the model should be a withdrawal movement.
     */
    public function asWithdrawal(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => WalletMovementType::WITHDRAWAL,
        ]);
    }

    /**
     * Indicate that the model should have a specific source currency.
     */
    public function ofSourceCurrency(CurrencyCode $currency): static
    {
        return $this->state(function (array $attributes) use ($currency) {
            /** @var CurrencyCode $randomTargetCurrency */
            $randomTargetCurrency = $this->faker->boolean(70) ? $currency : $this->faker->randomElement(CurrencyCode::cases());

            [
                'randomSourceAmount' => $randomSourceAmount,
                'randomFxGrossRate' => $randomFxGrossRate,
                'randomFxFeePercentage' => $randomFxFeePercentage,
                'randomFxNetRate' => $randomFxNetRate,
                'randomTargetAmount' => $randomTargetAmount,
            ] = $this->generateConversionValues($currency, $randomTargetCurrency);

            return [
                'source_currency' => $currency,
                'source_amount' => $randomSourceAmount,
                'fx_gross_rate' => $randomFxGrossRate,
                'fx_fee_percentage' => $randomFxFeePercentage,
                'fx_net_rate' => $randomFxNetRate,
                'target_currency' => $randomTargetCurrency,
                'target_amount' => $randomTargetAmount,
            ];
        });
    }

    /**
     * Indicate that the model should have target currency the same as the source currency.
     */
    public function sameCurrencyAsSource(): static
    {
        return $this->state(fn (array $attributes) => [
            'fx_gross_rate' => null,
            'fx_fee_percentage' => null,
            'fx_net_rate' => null,
            'target_currency' => $attributes['source_currency'],
            'target_amount' => $attributes['source_amount'],
        ]);
    }

    /**
     * Indicate that the model should have different target currency than the source currency.
     */
    public function differentCurrencyFromSource(): static
    {
        return $this->state(function (array $attributes) {
            $randomTargetCurrency = $this->faker->randomElement(
                array_filter(
                    CurrencyCode::cases(),
                    fn ($currency) => $currency !== $attributes['source_currency']
                )
            );

            [
                'randomSourceAmount' => $randomSourceAmount,
                'randomFxGrossRate' => $randomFxGrossRate,
                'randomFxFeePercentage' => $randomFxFeePercentage,
                'randomFxNetRate' => $randomFxNetRate,
                'randomTargetAmount' => $randomTargetAmount,
            ] = $this->generateConversionValues(
                $attributes['source_currency'],
                $randomTargetCurrency
            );

            return [
                'source_amount' => $randomSourceAmount,
                'fx_gross_rate' => $randomFxGrossRate,
                'fx_fee_percentage' => $randomFxFeePercentage,
                'fx_net_rate' => $randomFxNetRate,
                'target_currency' => $randomTargetCurrency,
                'target_amount' => $randomTargetAmount,
            ];
        });
    }

    /**
     * Generate random conversion values for the wallet movement with specified source and target currencies.
     *
     * @param  CurrencyCode  $sourceCurrency  Specific source currency code to use for the movement.
     * @param  CurrencyCode  $targetCurrency  Specific target currency code to use for the movement.
     * @return array{randomSourceAmount: Number, randomFxGrossRate: ?Number, randomFxFeePercentage: ?Number, randomFxNetRate: ?Number, randomTargetAmount: Number} An associative array containing the generated conversion values, including source and target amounts, and exchange rates.
     */
    private function generateConversionValues(CurrencyCode $sourceCurrency, CurrencyCode $targetCurrency): array
    {
        $randomSourceAmount = new Number(sprintf('%.6f', $this->faker->randomFloat(6, 1000, 100000)));

        if ($sourceCurrency === $targetCurrency) {
            $randomFxGrossRate = null;
            $randomFxFeePercentage = null;
            $randomFxNetRate = null;
        } else {
            $randomFxGrossRate = new Number(sprintf('%.6f', $this->faker->randomFloat(6, 0, 7)));
            $randomFxFeePercentage = new Number(sprintf('%.2f', $this->faker->randomFloat(2, 0, 0.05)));
            $randomFxNetRate = $randomFxGrossRate * (new Number('1') - $randomFxFeePercentage);
        }

        return [
            'randomSourceAmount' => $randomSourceAmount,
            'randomFxGrossRate' => $randomFxGrossRate,
            'randomFxFeePercentage' => $randomFxFeePercentage,
            'randomFxNetRate' => $randomFxNetRate,
            'randomTargetAmount' => $randomSourceAmount * ($randomFxNetRate ?? 1),
        ];
    }
}
