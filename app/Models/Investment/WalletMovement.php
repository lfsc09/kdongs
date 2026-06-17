<?php

namespace App\Models\Investment;

use App\Enums\Investments\CurrencyCode;
use App\Enums\Investments\WalletMovementType;
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
 * @property int $source_amount
 * @property float|null $fx_gross_rate
 * @property float|null $fx_fee_percentage
 * @property float|null $fx_net_rate
 * @property CurrencyCode $target_currency
 * @property int $target_amount
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
            'fx_gross_rate' => 'float',
            'fx_fee_percentage' => 'float',
            'fx_net_rate' => 'float',
            'target_currency' => CurrencyCode::class,
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
