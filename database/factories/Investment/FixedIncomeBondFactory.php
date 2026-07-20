<?php

namespace Database\Factories\Investment;

use App\Enums\Investment\CountryCode;
use App\Enums\Investment\CurrencyCode;
use App\Enums\Investment\DoneState;
use App\Enums\Investment\FixedIncome\AssetClass;
use App\Enums\Investment\FixedIncome\BondType;
use App\Enums\Investment\FixedIncome\IndexType;
use App\Enums\Investment\FixedIncome\InterestType;
use App\Models\Investment\FixedIncomeBond;
use App\Models\Investment\FixedIncomeBondTransaction;
use BcMath\Number;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FixedIncomeBond>
 */
class FixedIncomeBondFactory extends Factory
{
    private const RANDOM_MATURITY_DAYS_RANGE = [
        DoneState::DONE->value => ['from' => -365 * 5, 'to' => -1], // Done bonds: maturity between 5 years ago and 1 day ago
        DoneState::ACTIVE->value => ['from' => 0, 'to' => 365 * 5], // Active bonds: maturity between now and 5 years from now
        DoneState::TRANSFERRED->value => ['from' => -365 * 5, 'to' => 365 * 5], // Transferred bonds: maturity between 5 years ago and 5 years from now
    ];

    private const RANDOM_ENTER_DAYS_RANGE = ['from' => 1, 'to' => 365 * 3]; // Enter date between 1 day and 3 years before the maturity date

    /**
     * Default ranges for generating random transaction shares for each bond type.
     */
    private const RANDOM_TRANSACTION_SHARES_RANGE = [
        BondType::BRL_CDB->value => ['from' => 1, 'to' => 50],
        BondType::BRL_LCA->value => ['from' => 1, 'to' => 50],
        BondType::BRL_LCI->value => ['from' => 1, 'to' => 50],
        BondType::BRL_DEBENTURE->value => ['from' => 1, 'to' => 100],
        BondType::BRL_TREASURY_SELIC->value => ['from' => 0.01, 'to' => 3.5],
        BondType::BRL_TREASURY_IPCA->value => ['from' => 0.05, 'to' => 15.00],
        BondType::BRL_TREASURY_FIXED->value => ['from' => 0.10, 'to' => 20.00],
        BondType::USD_CORPORATE_BOND->value => ['from' => 1, 'to' => 50],
        BondType::USD_TREASURY_BILL->value => ['from' => 0.1, 'to' => 20],
        BondType::USD_TREASURY_NOTE->value => ['from' => 0.1, 'to' => 20],
    ];

    /**
     * Default ranges for generating random transaction unit prices for each bond type.
     */
    private const RANDOM_TRANSACTION_UNIT_PRICE_RANGE = [
        BondType::BRL_CDB->value => ['from' => 1000, 'to' => 1000],
        BondType::BRL_LCA->value => ['from' => 1000, 'to' => 1000],
        BondType::BRL_LCI->value => ['from' => 1000, 'to' => 1000],
        BondType::BRL_DEBENTURE->value => ['from' => 950, 'to' => 1100],
        BondType::BRL_TREASURY_SELIC->value => ['from' => 14500, 'to' => 15500],
        BondType::BRL_TREASURY_IPCA->value => ['from' => 2500, 'to' => 3800],
        BondType::BRL_TREASURY_FIXED->value => ['from' => 750, 'to' => 1050],
        BondType::USD_CORPORATE_BOND->value => ['from' => 950, 'to' => 1050],
        BondType::USD_TREASURY_BILL->value => ['from' => 970, 'to' => 1000],
        BondType::USD_TREASURY_NOTE->value => ['from' => 920, 'to' => 1020],
    ];

    /**
     * Default probability of selling all shares at once for each bond type.
     * This is used to determine whether to create a single sell transaction for all shares or multiple sell transactions for partial shares.
     */
    private const DEFAULT_PROBABILITY_OF_SELL_ALL_AT_ONCE = [
        BondType::BRL_CDB->value => 1.0,
        BondType::BRL_LCA->value => 1.0,
        BondType::BRL_LCI->value => 1.0,
        BondType::BRL_DEBENTURE->value => 0.5,
        BondType::BRL_TREASURY_SELIC->value => 0.3,
        BondType::BRL_TREASURY_IPCA->value => 0.3,
        BondType::BRL_TREASURY_FIXED->value => 0.3,
        BondType::USD_CORPORATE_BOND->value => 0.6,
        BondType::USD_TREASURY_BILL->value => 0.3,
        BondType::USD_TREASURY_NOTE->value => 0.3,
    ];

    /**
     * Default ranges for generating random sale price factors for each bond type.
     */
    private const RANDOM_SALE_PRICE_FACTOR_RANGE = [
        // Never negative (performance always >= 0%)
        BondType::BRL_CDB->value => ['from' => 1.005, 'to' => 1.300],
        BondType::BRL_LCA->value => ['from' => 1.005, 'to' => 1.250],
        BondType::BRL_LCI->value => ['from' => 1.005, 'to' => 1.250],

        // Almost never negative (performance can be slightly negative in rare stress scenarios)
        BondType::BRL_TREASURY_SELIC->value => ['from' => 0.998, 'to' => 1.200],
        BondType::USD_TREASURY_BILL->value => ['from' => 0.995, 'to' => 1.100],

        // Sometimes negative (performance can be negative in some scenarios)
        BondType::BRL_TREASURY_FIXED->value => ['from' => 0.800, 'to' => 1.350],
        BondType::BRL_TREASURY_IPCA->value => ['from' => 0.750, 'to' => 1.450],
        BondType::BRL_DEBENTURE->value => ['from' => 0.850, 'to' => 1.300],

        // American bonds can be more volatile, so they have a wider range of possible sale price factors
        BondType::USD_TREASURY_NOTE->value => ['from' => 0.850, 'to' => 1.250],
        BondType::USD_CORPORATE_BOND->value => ['from' => 0.800, 'to' => 1.300],
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        /** @var DoneState $randomDoneState */
        $randomDoneState = $this->faker->randomElement(DoneState::cases());
        /** @var CountryCode $randomCountryCode */
        $randomCountryCode = $this->faker->randomElement(CountryCode::cases());

        [
            'currency' => $currency,
            'assetClass' => $assetClass,
            'bondType' => $bondType,
            'interestType' => $interestType,
            'indexType' => $indexType,
        ] = $this->generateInfo($randomCountryCode);

        [
            'maturityDateUtc' => $maturityDateUtc,
            'enterDateUtc' => $enterDateUtc,
            'exitDateUtc' => $exitDateUtc,
        ] = $this->generateDatesBy($randomDoneState);

        return [
            'done_state' => $randomDoneState,
            'currency' => $currency,
            'country_code' => $randomCountryCode,
            'name' => $this->faker->word(),
            'asset_class' => $assetClass,
            'bond_type' => $bondType,
            'holder_institution' => $this->faker->company(),
            'issuer_institution' => $this->faker->company(),
            'interest_type' => $interestType,
            'index_type' => $indexType,
            'maturity_date_utc' => $maturityDateUtc,
            'enter_date_utc' => $enterDateUtc,
            'exit_date_utc' => $exitDateUtc,
            'details' => $this->faker->optional()->sentence(),
        ];
    }

    /**
     * Indicate that the model should be of a specific country code.
     */
    public function ofCountryCode(CountryCode $countryCode): static
    {
        return $this->state(function (array $attributes) use ($countryCode) {
            [
                'currency' => $currency,
                'assetClass' => $assetClass,
                'bondType' => $bondType,
                'interestType' => $interestType,
                'indexType' => $indexType,
            ] = $this->generateInfo($countryCode);

            return [
                'country_code' => $countryCode,
                'currency' => $currency,
                'asset_class' => $assetClass,
                'bond_type' => $bondType,
                'interest_type' => $interestType,
                'index_type' => $indexType,
            ];
        });
    }

    /**
     * Configure the factory to create related transactions after creating a FixedIncomeBond instance.
     */
    public function configure(): static
    {
        return $this->afterCreating(function (FixedIncomeBond $bond) {
            $transactionTotals = match ($bond->bond_type) {
                BondType::BRL_CDB,
                BondType::BRL_LCA,
                BondType::BRL_LCI => $this->generateTransactions(1, $bond),
                BondType::BRL_DEBENTURE,
                BondType::BRL_TREASURY_SELIC,
                BondType::BRL_TREASURY_IPCA,
                BondType::BRL_TREASURY_FIXED => $this->generateTransactions(random_int(1, 5), $bond),
                BondType::USD_CORPORATE_BOND,
                BondType::USD_TREASURY_BILL,
                BondType::USD_TREASURY_NOTE => $this->generateTransactions(random_int(1, 3), $bond),
            };

            $bond->total_shares_amount = $transactionTotals->boughtTotalSharesAmount;
            $bond->total_input_amount = $transactionTotals->boughtTotalInput;
            $bond->current_gross_amount = $transactionTotals->sellTotalGross;
            $bond->current_net_amount = $transactionTotals->sellTotalNet;

            $bond->save();
        });
    }

    /**
     * Generates buy and sell transactions for a given bond based on the specified number of buy transactions.
     *
     * @param  int  $numberOfBuyTransactions  The number of buy transactions to generate.
     * @param  FixedIncomeBond  $bond  The bond for which to generate transactions.
     * @return object{boughtTotalSharesAmount: Number, boughtTotalInput: Number, boughtUnitPrices: array<Number>, sellTotalGross: Number, sellTotalNet: Number} An object containing the total shares bought, total input amount, an array of unit prices for the buy transactions, total gross amount from sell transactions, and total net amount from sell transactions.
     */
    private function generateTransactions(int $numberOfBuyTransactions, FixedIncomeBond $bond): object
    {
        $buyTransactionDate = $bond->enter_date_utc;
        $transactionDateEndLimit = $bond->exit_date_utc?->subDay() ?? Carbon::now();

        $transactionDetails = new class(new Number(0), new Number(0), [], new Number(0), new Number(0))
        {
            public function __construct(
                public Number $boughtTotalSharesAmount,
                public Number $boughtTotalInput,
                public array $boughtUnitPrices,
                public Number $sellTotalGross,
                public Number $sellTotalNet
            ) {}
        };

        for ($i = 0; $i < $numberOfBuyTransactions; $i++) {
            if ($i > 0) {
                $nextPossibleBuyLowerLimit = $buyTransactionDate->copy()->addDay();
                if ($nextPossibleBuyLowerLimit->diffInDays($transactionDateEndLimit) > 0) {
                    $buyTransactionDate = Carbon::createFromTimestamp(random_int($nextPossibleBuyLowerLimit->timestamp, $transactionDateEndLimit->timestamp));
                }
            }

            $buyTransactionSharesAmount = new Number(sprintf('%.6f', $this->faker->randomFloat(6, self::RANDOM_TRANSACTION_SHARES_RANGE[$bond->bond_type->value]['from'], self::RANDOM_TRANSACTION_SHARES_RANGE[$bond->bond_type->value]['to'])));
            $buyTransactionUnitPrice = new Number(sprintf('%.6f', $this->faker->randomFloat(6, self::RANDOM_TRANSACTION_UNIT_PRICE_RANGE[$bond->bond_type->value]['from'], self::RANDOM_TRANSACTION_UNIT_PRICE_RANGE[$bond->bond_type->value]['to'])));

            FixedIncomeBondTransaction::factory()
                ->count(1)
                ->asBuy()
                ->sameCurrencyAsWalletResidency()
                ->create([
                    'fixed_income_bond_id' => $bond->id,
                    'date_utc' => $buyTransactionDate,
                ]);

            $transactionDetails->boughtTotalSharesAmount += $buyTransactionSharesAmount;
            $transactionDetails->boughtUnitPrices[] = $buyTransactionUnitPrice;
            $transactionDetails->boughtTotalInput += $buyTransactionUnitPrice * $buyTransactionSharesAmount;
        }

        if ($numberOfBuyTransactions && $bond->done_state->isDone()) {
            $buyTotalSharesAmount = $transactionDetails->boughtTotalSharesAmount;

            // While there are shares left to sell, create sell transactions
            while ($buyTotalSharesAmount->compare(0) === 1) {
                $nextPossibleSellLowerLimit = $buyTransactionDate->copy()->addDay();
                $remainingDaysUntilExit = $nextPossibleSellLowerLimit->diffInDays($bond->exit_date_utc);
                $sellAllAtOnce = $remainingDaysUntilExit <= 0 || $this->faker->boolean(self::DEFAULT_PROBABILITY_OF_SELL_ALL_AT_ONCE[$bond->bond_type->value] * 100);

                $sellTransactionDate = $sellAllAtOnce
                    ? $bond->exit_date_utc
                    : Carbon::createFromTimestamp(random_int($nextPossibleSellLowerLimit->timestamp, $bond->exit_date_utc->timestamp));

                $randomSharesAmount = $sellAllAtOnce
                    ? $transactionDetails->boughtTotalSharesAmount
                    : new Number(sprintf('%.6f', $this->faker->randomFloat(6, self::RANDOM_TRANSACTION_SHARES_RANGE[$bond->bond_type->value]['from'], (float) $transactionDetails->boughtTotalSharesAmount->value)));

                $randomUnitPriceGainPercentage = new Number(sprintf('%.6f', $this->faker->randomFloat(6, self::RANDOM_SALE_PRICE_FACTOR_RANGE[$bond->bond_type->value]['from'], self::RANDOM_SALE_PRICE_FACTOR_RANGE[$bond->bond_type->value]['to'])));
                $avgUnitPrice = array_reduce(
                    $transactionDetails->boughtUnitPrices,
                    fn (Number $carry, Number $price) => $carry + $price,
                    new Number(0)
                ) / count($transactionDetails->boughtUnitPrices);
                $randomUnitPrice = $avgUnitPrice * $randomUnitPriceGainPercentage;

                $sellTransactionsCollection = FixedIncomeBondTransaction::factory()
                    ->count(1)
                    ->asSell($randomUnitPrice, $randomSharesAmount)
                    ->sameCurrencyAsWalletResidency()
                    ->create([
                        'fixed_income_bond_id' => $bond->id,
                        'date_utc' => $sellTransactionDate,
                    ]);

                $grossAmount = $randomUnitPrice * $randomSharesAmount;
                $transactionDetails->sellTotalGross += $grossAmount;

                $sellTransaction = $sellTransactionsCollection->first();
                $transactionDetails->sellTotalNet += $grossAmount - $sellTransaction->taxes - $sellTransaction->fees;

                $buyTotalSharesAmount -= $randomSharesAmount;
            }
        }

        return $transactionDetails;
    }

    /**
     * Generates bond information based on the provided country code.
     *
     * @param  CountryCode  $countryCode  The country code for which to generate bond information.
     * @return array{currency: CurrencyCode, assetClass: AssetClass, bondType: BondType, interestType: InterestType, indexType: IndexType} An associative array containing the generated bond information, including currency, asset class, bond type, interest type, and index type.
     *
     * @throws \InvalidArgumentException If the provided country code is not supported.
     */
    private function generateInfo(CountryCode $countryCode): array
    {
        switch ($countryCode) {
            case CountryCode::BR:
                $randomBondType = $this->faker->randomElement([
                    BondType::BRL_CDB,
                    BondType::BRL_LCA,
                    BondType::BRL_LCI,
                    BondType::BRL_DEBENTURE,
                    BondType::BRL_TREASURY_SELIC,
                    BondType::BRL_TREASURY_IPCA,
                    BondType::BRL_TREASURY_FIXED,
                ]);

                return [
                    'currency' => CurrencyCode::BRL,
                    'assetClass' => $this->generateAssetClassBy($randomBondType),
                    'bondType' => $randomBondType,
                    'interestType' => $this->generateInterestTypeBy($randomBondType),
                    'indexType' => $this->generateIndexTypeBy($randomBondType),
                ];
            case CountryCode::US:
                $randomBondType = $this->faker->randomElement([
                    BondType::USD_CORPORATE_BOND,
                    BondType::USD_TREASURY_BILL,
                    BondType::USD_TREASURY_NOTE,
                ]);

                return [
                    'currency' => CurrencyCode::USD,
                    'assetClass' => $this->generateAssetClassBy($randomBondType),
                    'bondType' => $randomBondType,
                    'interestType' => $this->generateInterestTypeBy($randomBondType),
                    'indexType' => $this->generateIndexTypeBy($randomBondType),
                ];
            default:
                throw new \InvalidArgumentException("Unsupported country code: {$countryCode}");
        }
    }

    /**
     * Generates the asset class based on the provided bond type.
     *
     * @param  BondType  $bondType  The bond type for which to determine the asset class.
     * @return AssetClass The corresponding asset class for the given bond type.
     *
     * @throws \InvalidArgumentException If the provided bond type is not supported.
     */
    private function generateAssetClassBy(BondType $bondType): AssetClass
    {
        switch ($bondType) {
            case BondType::BRL_CDB:
            case BondType::BRL_LCA:
            case BondType::BRL_LCI:
            case BondType::BRL_DEBENTURE:
            case BondType::USD_CORPORATE_BOND:
                return AssetClass::PRIVATE;
            case BondType::BRL_TREASURY_SELIC:
            case BondType::BRL_TREASURY_IPCA:
            case BondType::BRL_TREASURY_FIXED:
            case BondType::USD_TREASURY_BILL:
            case BondType::USD_TREASURY_NOTE:
                return AssetClass::PUBLIC;
            default:
                throw new \InvalidArgumentException("Unsupported bond type: {$bondType}");
        }
    }

    /**
     * Generates the interest type based on the provided bond type.
     *
     * @param  BondType  $bondType  The bond type for which to determine the interest type.
     * @return InterestType The corresponding interest type for the given bond type.
     *
     * @throws \InvalidArgumentException If the provided bond type is not supported.
     */
    private function generateInterestTypeBy(BondType $bondType): InterestType
    {
        switch ($bondType) {
            case BondType::BRL_CDB:
            case BondType::BRL_LCA:
            case BondType::BRL_LCI:
            case BondType::BRL_DEBENTURE:
            case BondType::USD_CORPORATE_BOND:
                return $this->faker->randomElement([
                    InterestType::FIXED,
                    InterestType::FLOATING,
                ]);
            case BondType::BRL_TREASURY_SELIC:
            case BondType::BRL_TREASURY_IPCA:
                return InterestType::FLOATING;
            case BondType::BRL_TREASURY_FIXED:
            case BondType::USD_TREASURY_BILL:
            case BondType::USD_TREASURY_NOTE:
                return InterestType::FIXED;
            default:
                throw new \InvalidArgumentException("Unsupported bond type: {$bondType}");
        }
    }

    /**
     * Generates the index type based on the provided bond type.
     *
     * @param  BondType  $bondType  The bond type for which to determine the index type.
     * @return IndexType The corresponding index type for the given bond type.
     *
     * @throws \InvalidArgumentException If the provided bond type is not supported.
     */
    private function generateIndexTypeBy(BondType $bondType): IndexType
    {
        switch ($bondType) {
            case BondType::BRL_CDB:
            case BondType::BRL_LCA:
            case BondType::BRL_LCI:
            case BondType::BRL_DEBENTURE:
                return $this->faker->randomElement([
                    IndexType::BRL_CDI,
                    IndexType::BRL_IPCA,
                    IndexType::FIXED,
                ]);
            case BondType::USD_CORPORATE_BOND:
                return IndexType::FIXED;
            case BondType::BRL_TREASURY_SELIC:
                return IndexType::BRL_SELIC;
            case BondType::BRL_TREASURY_IPCA:
                return IndexType::BRL_IPCA;
            case BondType::BRL_TREASURY_FIXED:
            case BondType::USD_TREASURY_BILL:
            case BondType::USD_TREASURY_NOTE:
                return IndexType::FIXED;
            default:
                throw new \InvalidArgumentException("Unsupported bond type: {$bondType}");
        }
    }

    /**
     * Generates maturity, enter, and exit dates based on the provided done state.
     *
     * @param  DoneState  $doneState  The done state of the bond (e.g., active, done, transferred).
     * @return array{maturityDateUtc: Carbon, enterDateUtc: Carbon, exitDateUtc: ?Carbon} An associative array containing the generated maturity date, enter date, and exit date (if applicable).
     */
    private function generateDatesBy(DoneState $doneState): array
    {
        if ($doneState->isDone()) {
            $maturityDateUtc = Carbon::now()->addDays(random_int(self::RANDOM_MATURITY_DAYS_RANGE[DoneState::DONE->value]['from'], self::RANDOM_MATURITY_DAYS_RANGE[DoneState::DONE->value]['to']));
        } elseif ($doneState->isActive()) {
            $maturityDateUtc = Carbon::now()->addDays(random_int(self::RANDOM_MATURITY_DAYS_RANGE[DoneState::ACTIVE->value]['from'], self::RANDOM_MATURITY_DAYS_RANGE[DoneState::ACTIVE->value]['to']));
        } else {
            $maturityDateUtc = Carbon::now()->addDays(random_int(self::RANDOM_MATURITY_DAYS_RANGE[DoneState::TRANSFERRED->value]['from'], self::RANDOM_MATURITY_DAYS_RANGE[DoneState::TRANSFERRED->value]['to']));
        }
        // Random enter date
        $enterDateUtc = $maturityDateUtc->copy()->subDays(random_int(self::RANDOM_ENTER_DAYS_RANGE['from'], self::RANDOM_ENTER_DAYS_RANGE['to']));
        // If enter date in the future, set it to now
        if ($enterDateUtc->isFuture()) {
            $enterDateUtc = Carbon::now();
        }

        if ($doneState->isDone()) {
            // Random exit date between the enter date and the maturity date
            $exitDateUtc = $enterDateUtc->copy()->addDays(random_int(1, $enterDateUtc->diffInDays($maturityDateUtc)));
        } else {
            $exitDateUtc = null;
        }

        return [
            'maturityDateUtc' => $maturityDateUtc,
            'enterDateUtc' => $enterDateUtc,
            'exitDateUtc' => $exitDateUtc,
        ];
    }
}
