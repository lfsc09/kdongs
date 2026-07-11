<?php

namespace Database\Factories\Investment;

use App\Enums\Investments\CurrencyCode;
use App\Enums\Investments\WalletMovementType;
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
        $randomType = $this->faker->randomElement(WalletMovementType::cases());

        [
            'randomOriginCurrency' => $randomOriginCurrency,
            'randomOriginAmount' => $randomOriginAmount,
            'randomOriginExchGrossRate' => $randomOriginExchGrossRate,
            'randomOriginExchOpFeePerc' => $randomOriginExchOpFeePerc,
            'randomOriginExchVetRate' => $randomOriginExchVetRate,
            'randomResultCurrency' => $randomResultCurrency,
            'randomResultAmount' => $randomResultAmount,
        ] = $this->generateConversionValues($randomType);

        return [
            'type' => $randomType,
            'institution' => $this->faker->company(),
            'origin_currency_code' => $randomOriginCurrency,
            'origin_amount' => $randomOriginAmount,
            'origin_exch_gross_rate' => $randomOriginExchGrossRate,
            'origin_exch_op_fee_perc' => $randomOriginExchOpFeePerc,
            'origin_exch_vet_rate' => $randomOriginExchVetRate,
            'result_currency_code' => $randomResultCurrency,
            'result_amount' => $randomResultAmount,
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
            // Ensure the origin amount is positive for deposits
            'origin_amount' => abs($attributes['origin_amount']),
            'result_amount' => abs($attributes['result_amount']),
        ]);
    }

    /**
     * Indicate that the model should be a withdrawal movement.
     */
    public function asWithdrawal(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => WalletMovementType::WITHDRAWAL,
            // Ensure the origin amount is negative for withdrawals
            'origin_amount' => -abs($attributes['origin_amount']),
            'result_amount' => -abs($attributes['result_amount']),
        ]);
    }

    /**
     * Indicate that the model should have the same currency for origin and result.
     */
    public function sameCurrency(): static
    {
        return $this->state(fn (array $attributes) => [
            'origin_exch_gross_rate' => null,
            'origin_exch_op_fee_perc' => null,
            'origin_exch_vet_rate' => null,
            'result_currency_code' => $attributes['origin_currency_code'],
            'result_amount' => $attributes['origin_amount'],
        ]);
    }

    /**
     * Indicate that the model should have different currencies for origin and result, with appropriate conversion values.
     */
    public function differentCurrency(): static
    {
        return $this->state(function (array $attributes) {
            $randomResultCurrency = $this->faker->randomElement(
                array_filter(
                    CurrencyCode::cases(),
                    fn ($currency) => $currency !== $attributes['origin_currency_code']
                )
            );

            [
                'randomOriginAmount' => $randomOriginAmount,
                'randomOriginExchGrossRate' => $randomOriginExchGrossRate,
                'randomOriginExchOpFeePerc' => $randomOriginExchOpFeePerc,
                'randomOriginExchVetRate' => $randomOriginExchVetRate,
                'randomResultAmount' => $randomResultAmount,
            ] = $this->generateConversionValues(
                $attributes['type'],
                $attributes['origin_currency_code'],
                $randomResultCurrency
            );

            return [
                'origin_amount' => $randomOriginAmount,
                'origin_exch_gross_rate' => $randomOriginExchGrossRate,
                'origin_exch_op_fee_perc' => $randomOriginExchOpFeePerc,
                'origin_exch_vet_rate' => $randomOriginExchVetRate,
                'result_currency_code' => $randomResultCurrency,
                'result_amount' => $randomResultAmount,
            ];
        });
    }

    /**
     * Generate random conversion values for the wallet movement based on the type (deposit or withdrawal) and optionally specified origin and result currencies.
     *
     * @param  WalletMovementType  $type  The type of wallet movement (deposit or withdrawal).
     * @param  CurrencyCode|null  $originCurrency  Optional specific origin currency code to use for the movement.
     * @param  CurrencyCode|null  $resultCurrency  Optional specific result currency code to use for the movement.
     * @return array{randomOriginCurrency: CurrencyCode, randomOriginAmount: Number, randomOriginExchGrossRate: ?Number, randomOriginExchOpFeePerc: ?Number, randomOriginExchVetRate: ?Number, randomResultCurrency: CurrencyCode, randomResultAmount: Number} An associative array containing the generated conversion values, including origin and result currencies, amounts, and exchange rates.
     */
    private function generateConversionValues(WalletMovementType $type, ?CurrencyCode $originCurrency = null, ?CurrencyCode $resultCurrency = null): array
    {
        /** @var CurrencyCode $randomOriginCurrency */
        $randomOriginCurrency = $originCurrency ?? $this->faker->randomElement(CurrencyCode::cases());
        /** @var CurrencyCode $randomResultCurrency */
        $randomResultCurrency = $resultCurrency ?? $this->faker->randomElement(CurrencyCode::cases());

        $randomOriginAmount = new Number(
            $type->isDeposit()
                ? $this->faker->numberBetween(10000, 10000000)
                : -$this->faker->numberBetween(10000, 10000000)
        );

        if ($randomOriginCurrency === $randomResultCurrency) {
            $randomOriginExchGrossRate = null;
            $randomOriginExchOpFeePerc = null;
            $randomOriginExchVetRate = null;
        } else {
            $randomOriginExchGrossRate = new Number($this->faker->randomFloat(6, 0, 7));
            $randomOriginExchOpFeePerc = new Number($this->faker->randomFloat(2, 0, 0.05));
            $randomOriginExchVetRate = $this->calculateExchVetRate($randomOriginExchGrossRate, $randomOriginExchOpFeePerc);
        }

        return [
            'randomOriginCurrency' => $randomOriginCurrency,
            'randomOriginAmount' => $randomOriginAmount,
            'randomOriginExchGrossRate' => $randomOriginExchGrossRate,
            'randomOriginExchOpFeePerc' => $randomOriginExchOpFeePerc,
            'randomOriginExchVetRate' => $randomOriginExchVetRate,
            'randomResultCurrency' => $randomResultCurrency,
            'randomResultAmount' => $randomOriginAmount * ($randomOriginExchVetRate ?? 1),
        ];
    }
}
