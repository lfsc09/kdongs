<?php

namespace App\Models\Investment;

use App\Enums\Investment\FixedIncome\TransactionType;
use BcMath\Number;
use Carbon\Carbon;
use Database\Factories\Investment\FixedIncomeBondTransactionFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Override;

/**
 * @property string $id
 * @property string $fixed_income_bond_id
 * @property TransactionType $transaction_type
 * @property Carbon $date_utc
 * @property Number $index_value
 * @property Number $unit_price
 * @property Number $shares_amount
 * @property Number $taxes
 * @property Number $fees
 * @property Number $fx_rate_to_residency
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class FixedIncomeBondTransaction extends Model
{
    /** @use HasFactory<FixedIncomeBondTransactionFactory> */
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
            'transaction_type' => TransactionType::class,
            'date_utc' => 'datetime',
            'index_value' => Number::class,
            'unit_price' => Number::class,
            'shares_amount' => Number::class,
            'taxes' => Number::class,
            'fees' => Number::class,
            'fx_rate_to_residency' => Number::class,
        ];
    }

    /**
     * Get the fixed income bond that owns the transaction.
     */
    public function bond()
    {
        return $this->belongsTo(FixedIncomeBond::class);
    }
}
