<?php

namespace Database\Factories\Investment;

use App\Enums\Investment\FixedIncome\TransactionType;
use App\Models\Investment\FixedIncomeBondTransaction;
use BcMath\Number;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FixedIncomeBondTransaction>
 */
class FixedIncomeBondTransactionFactory extends Factory
{
    private const DEFAULT_PROBABILITY_TO_PAY_FEES = 0.3; // 30% chance to pay fees

    private const DEFAULT_PROBABILITY_TO_SAME_CURRENCY = 0.7; // 70% chance to be the same currency as the residency

    private const RANDOM_DAYS_RANGE = ['from' => -365 * 5, 'to' => 0]; // Enter date between 5 years ago and now

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        /** @var TransactionType $randomTransactionType */
        $randomTransactionType = $this->faker->randomElement(TransactionType::cases());
        $randomUnitPrice = new Number(sprintf('%.6f', $this->faker->randomFloat(6, 50, 150)));
        $randomSharesAmount = new Number(sprintf('%.6f', $this->faker->randomFloat(6, 1, 1000)));

        return [
            'transaction_type' => $randomTransactionType,
            'date_utc' => Carbon::now()->addDays(random_int(self::RANDOM_DAYS_RANGE['from'], self::RANDOM_DAYS_RANGE['to'])),
            'index_value' => $this->faker->randomFloat(2, 0.005, 0.15),
            'unit_price' => $randomUnitPrice,
            'shares_amount' => $randomSharesAmount,
            'taxes' => $this->calculateTaxes($randomUnitPrice, $randomSharesAmount, $randomTransactionType),
            'fees' => $this->calculateFees($randomUnitPrice, $randomSharesAmount),
            'fx_rate_to_residency' => $this->generateFxRateToResidency(),
            'details' => $this->faker->optional()->sentence(),
        ];
    }

    /**
     * Indicate that the transaction is a buy transaction with optional unit price and shares amount.
     *
     * @param  Number|null  $unitPrice  Optional unit price for the buy transaction. If null, a random value will be generated.
     * @param  Number|null  $sharesAmount  Optional shares amount for the buy transaction. If null, a random value will be generated.
     */
    public function asBuy(?Number $unitPrice = null, ?Number $sharesAmount = null): static
    {
        if ($unitPrice !== null && $unitPrice <= 0 || $sharesAmount !== null && $sharesAmount <= 0) {
            throw new \InvalidArgumentException('Unit price and shares amount must be greater than zero for a buy transaction.');
        }

        return $this->state(function (array $attributes) use ($unitPrice, $sharesAmount) {
            $unitPrice = $unitPrice ?? $attributes['unit_price'];
            $sharesAmount = $sharesAmount ?? $attributes['shares_amount'];

            if ($unitPrice !== null || $sharesAmount !== null) {
                $fees = $this->calculateFees($unitPrice, $sharesAmount);
            }

            return [
                'transaction_type' => TransactionType::BUY->value,
                'unit_price' => $unitPrice,
                'shares_amount' => $sharesAmount,
                'taxes' => 0,
                'fees' => $fees ?? $attributes['fees'],
            ];
        });
    }

    /**
     * Indicate that the transaction is a sell transaction with optional unit price and shares amount.
     *
     * @param  Number|null  $unitPrice  Optional unit price for the sell transaction. If null, a random value will be generated.
     * @param  Number|null  $sharesAmount  Optional shares amount for the sell transaction. If null, a random value will be generated.
     */
    public function asSell(?Number $unitPrice = null, ?Number $sharesAmount = null): static
    {
        if ($unitPrice !== null && $unitPrice <= 0 || $sharesAmount !== null && $sharesAmount <= 0) {
            throw new \InvalidArgumentException('Unit price and shares amount must be greater than zero for a sell transaction.');
        }

        return $this->state(function (array $attributes) use ($unitPrice, $sharesAmount) {
            $unitPrice = $unitPrice ?? $attributes['unit_price'];
            $sharesAmount = $sharesAmount ?? $attributes['shares_amount'];

            if ($unitPrice !== null || $sharesAmount !== null) {
                $fees = $this->calculateFees($unitPrice, $sharesAmount);
            }

            $taxes = $this->calculateTaxes($unitPrice, $sharesAmount, TransactionType::SELL);

            return [
                'transaction_type' => TransactionType::SELL->value,
                'unit_price' => $unitPrice,
                'shares_amount' => $sharesAmount,
                'taxes' => $taxes,
                'fees' => $fees ?? $attributes['fees'],
            ];
        });
    }

    /**
     * Indicate that the transaction is in the same currency as the residency currency, setting the foreign exchange rate to 1.
     */
    public function sameCurrencyAsWalletResidency(): static
    {
        return $this->state(fn (array $attributes) => [
            'fx_rate_to_residency' => 1,
        ]);
    }

    /**
     * Calculates the taxes for a transaction based on the unit price, shares amount, and transaction type.
     *
     * @param  Number  $unitPrice  The unit price of the asset.
     * @param  Number  $sharesAmount  The number of shares.
     * @param  TransactionType  $transactionType  The type of the transaction (buy or sell).
     * @return Number The calculated taxes.
     */
    private function calculateTaxes(Number $unitPrice, Number $sharesAmount, TransactionType $transactionType): Number
    {
        if ($transactionType->isSell()) {
            // Random taxes percentage between 0% and 5%
            $randomTaxesPercentage = new Number(sprintf('%.6f', $this->faker->randomFloat(6, 0, 0.05)));

            return $unitPrice * $sharesAmount * $randomTaxesPercentage;
        }

        return new Number(0);
    }

    /**
     * Calculates the fees for a transaction based on the unit price, shares amount, and an optional probability to pay fees.
     *
     * @param  Number  $unitPrice  The unit price of the asset.
     * @param  Number  $sharesAmount  The number of shares.
     * @param  float|null  $probToPay  Optional probability to pay fees. If null, a default 30% chance is used.
     * @return Number The calculated fees.
     */
    private function calculateFees(Number $unitPrice, Number $sharesAmount, ?float $probToPay = null): Number
    {
        // 30% chance to pay fees
        $probToPayFees = $probToPay ?? $this->faker->boolean(self::DEFAULT_PROBABILITY_TO_PAY_FEES * 100);
        if (! $probToPayFees) {
            return new Number(0);
        }

        // Random fees percentage between 0% and 2%
        $randomFeesPercentage = new Number(sprintf('%.6f', $this->faker->randomFloat(6, 0, 0.02)));

        return $unitPrice * $sharesAmount * $randomFeesPercentage;
    }

    /**
     * Generates a foreign exchange rate to the residency currency based on an optional probability to be the same currency.
     *
     * @param  float|null  $probToSameCurrency  Optional probability to be the same currency as the residency. If null, a default 70% chance is used.
     * @return Number The generated foreign exchange rate to the residency currency.
     */
    private function generateFxRateToResidency(?float $probToSameCurrency = null): Number
    {
        // 70% chance to be the same currency as the residency
        $probToSameCurrency = $probToSameCurrency ?? $this->faker->boolean(self::DEFAULT_PROBABILITY_TO_SAME_CURRENCY * 100);
        if ($probToSameCurrency) {
            return new Number(1);
        }

        // Random FX rate between 0.5 and 5.0
        return new Number(sprintf('%.6f', $this->faker->randomFloat(6, 0.5, 5.0)));
    }

    /**
     * Calculates the tax fee for a given number of days held.
     * Brazilian tax rates decrease over time, and this function returns the corresponding fee based on the number of days the investment has been held.
     *
     * @param  int  $daysHeld  The number of days the investment has been held.
     * @return Number The tax fee as a decimal value.
     */
    private function brlTaxFee(int $daysHeld): Number
    {
        if ($daysHeld <= 180) {
            return new Number('0.225'); // 22.5% for up to 180 days
        } elseif ($daysHeld <= 360) {
            return new Number('0.20'); // 20% for 181 to 360 days
        } elseif ($daysHeld <= 720) {
            return new Number('0.175'); // 17.5% for 361 to 720 days
        } else {
            return new Number('0.15'); // 15% for more than 720 days
        }
    }

    /**
     * Calculates the IOF (Tax on Financial Operations) fee for a given number of days held.
     * Brazilian IOF rates decrease over time, and this function returns the corresponding fee based on the number of days the investment has been held.
     *
     * @param  int  $daysHeld  The number of days the investment has been held.
     * @return Number The IOF fee as a decimal value.
     */
    private function brlIOFFee(int $daysHeld): Number
    {
        switch ($daysHeld) {
            case 1:
                return new Number('0.96');
            case 2:
                return new Number('0.93');
            case 3:
                return new Number('0.90');
            case 4:
                return new Number('0.86');
            case 5:
                return new Number('0.83');
            case 6:
                return new Number('0.80');
            case 7:
                return new Number('0.76');
            case 8:
                return new Number('0.73');
            case 9:
                return new Number('0.70');
            case 10:
                return new Number('0.66');
            case 11:
                return new Number('0.63');
            case 12:
                return new Number('0.60');
            case 13:
                return new Number('0.56');
            case 14:
                return new Number('0.53');
            case 15:
                return new Number('0.50');
            case 16:
                return new Number('0.46');
            case 17:
                return new Number('0.43');
            case 18:
                return new Number('0.40');
            case 19:
                return new Number('0.36');
            case 20:
                return new Number('0.33');
            case 21:
                return new Number('0.30');
            case 22:
                return new Number('0.26');
            case 23:
                return new Number('0.23');
            case 24:
                return new Number('0.20');
            case 25:
                return new Number('0.16');
            case 26:
                return new Number('0.13');
            case 27:
                return new Number('0.10');
            case 28:
                return new Number('0.06');
            case 29:
                return new Number('0.03');
            default:
                return new Number('0');
        }
    }
}
