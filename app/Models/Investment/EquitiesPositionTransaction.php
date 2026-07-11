<?php

namespace App\Models\Investment;

use App\Enums\Investment\EquitiesPosition\TransactionType;
use BcMath\Number;
use Carbon\Carbon;
use Database\Factories\Investment\EquitiesPositionTransactionFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Override;

/**
 * @property string $id
 * @property string $equity_position_id
 * @property TransactionType $transaction_type
 * @property Carbon $date_utc
 * @property Number|null $shares_amount
 * @property Number|null $price_quote
 * @property Number|null $cash_amount
 * @property Number|null $factor
 * @property Number|null $taxes
 * @property Number|null $fees
 * @property Carbon|null $date_com_utc
 * @property Carbon|null $date_payment_utc
 * @property string|null $other_institution
 * @property Number $fx_rate_to_residency
 * @property string|null $details
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class EquitiesPositionTransaction extends Model
{
    /** @use HasFactory<EquitiesPositionTransactionFactory> */
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
            'shares_amount' => Number::class,
            'price_quote' => Number::class,
            'cash_amount' => Number::class,
            'factor' => Number::class,
            'taxes' => Number::class,
            'fees' => Number::class,
            'date_com_utc' => 'datetime',
            'date_payment_utc' => 'datetime',
            'fx_rate_to_residency' => Number::class,
        ];
    }

    /**
     * Get the equities position that owns the transaction.
     */
    public function position()
    {
        return $this->belongsTo(EquitiesPosition::class);
    }
}
