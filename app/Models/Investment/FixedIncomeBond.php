<?php

namespace App\Models\Investment;

use App\Enums\Investment\CountryCode;
use App\Enums\Investment\DoneState;
use App\Enums\Investment\FixedIncome\AssetClass;
use App\Enums\Investment\FixedIncome\BondType;
use App\Enums\Investment\FixedIncome\IndexType;
use App\Enums\Investment\FixedIncome\InterestType;
use App\Enums\Investments\CurrencyCode;
use BcMath\Number;
use Carbon\Carbon;
use Database\Factories\Investment\FixedIncomeBondFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Override;

/**
 * @property string $id
 * @property string $wallet_id
 * @property DoneState $done_state
 * @property CurrencyCode $currency
 * @property CountryCode $country_code
 * @property string $name
 * @property AssetClass $asset_class
 * @property BondType $bond_type
 * @property string $holder_institution
 * @property string $issuer_institution
 * @property InterestType $interest_type
 * @property IndexType $index_type
 * @property Carbon $maturity_date_utc
 * @property Carbon $enter_date_utc
 * @property Carbon|null $exit_date_utc
 * @property Number $total_shares_amount
 * @property Number $total_input_amount
 * @property Number $current_gross_amount
 * @property Number $current_net_amount
 * @property string|null $details
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class FixedIncomeBond extends Model
{
    /** @use HasFactory<FixedIncomeBondFactory> */
    use HasFactory, HasUuids;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    #[Override]
    protected function casts(): array
    {
        return [
            'done_state' => DoneState::class,
            'currency' => CurrencyCode::class,
            'country_code' => CountryCode::class,
            'asset_class' => AssetClass::class,
            'bond_type' => BondType::class,
            'interest_type' => InterestType::class,
            'index_type' => IndexType::class,
            'maturity_date_utc' => 'datetime',
            'enter_date_utc' => 'datetime',
            'exit_date_utc' => 'datetime',
            'total_shares_amount' => Number::class,
            'total_input_amount' => Number::class,
            'current_gross_amount' => Number::class,
            'current_net_amount' => Number::class,
        ];
    }

    /**
     * Get the wallet that owns the fixed income bond.
     */
    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

    /**
     * Get the transactions associated with the fixed income bond.
     */
    public function transactions()
    {
        return $this->hasMany(FixedIncomeBondTransaction::class);
    }
}
