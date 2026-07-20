<?php

namespace App\Models\Investment;

use App\Casts\BcNumberCast;
use App\Enums\Investment\CountryCode;
use App\Enums\Investment\CurrencyCode;
use App\Enums\Investment\DoneState;
use App\Enums\Investment\EquitiesPosition\AssetType;
use BcMath\Number;
use Carbon\Carbon;
use Database\Factories\Investment\EquitiesPositionFactory;
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
 * @property string $holder_institution
 * @property string $asset_ticker
 * @property AssetType $asset_type
 * @property Number $total_shares_amount
 * @property Number $average_price
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class EquitiesPosition extends Model
{
    /** @use HasFactory<EquitiesPositionFactory> */
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
            'asset_type' => AssetType::class,
            'total_shares_amount' => BcNumberCast::class,
            'average_price' => BcNumberCast::class,
        ];
    }

    /**
     * Get the wallet that owns the equities position.
     */
    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }
}
