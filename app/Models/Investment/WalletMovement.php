<?php

namespace App\Models\Investment;

use App\Enums\Investments\CurrencyCode;
use App\Enums\Investments\WalletMovementType;
use BcMath\Number;
use Database\Factories\Investment\WalletMovementFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Override;

/**
 * @property string $id
 * @property string $wallet_id
 * @property WalletMovementType $type
 * @property string|null $institution
 * @property CurrencyCode $source_currency
 * @property Number $source_amount
 * @property Number|null $fx_gross_rate
 * @property Number|null $fx_fee_percentage
 * @property Number|null $fx_net_rate
 * @property CurrencyCode $target_currency
 * @property Number $target_amount
 * @property string|null $details
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class WalletMovement extends Model
{
    /** @use HasFactory<WalletMovementFactory> */
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
            'type' => WalletMovementType::class,
            'source_currency' => CurrencyCode::class,
            'source_amount' => Number::class,
            'fx_gross_rate' => Number::class,
            'fx_fee_percentage' => Number::class,
            'fx_net_rate' => Number::class,
            'target_currency' => CurrencyCode::class,
            'target_amount' => Number::class,
        ];
    }

    /**
     * Get the wallet that owns the movement.
     */
    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }
}
