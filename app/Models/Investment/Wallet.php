<?php

namespace App\Models\Investment;

use App\Enums\Investments\CurrencyCode;
use App\Models\User;
use Database\Factories\Investment\WalletFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Override;

/**
 * @property string $id
 * @property string $user_id
 * @property string $name
 * @property CurrencyCode $view_currency
 * @property CurrencyCode $fiscal_residence_currency
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */
class Wallet extends Model
{
    /** @use HasFactory<WalletFactory> */
    use HasFactory, HasUuids, SoftDeletes;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    #[Override]
    protected function casts(): array
    {
        return [
            'view_currency' => CurrencyCode::class,
            'fiscal_residence_currency' => CurrencyCode::class,
        ];
    }

    /**
     * Get the user that owns the wallet.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the movements associated with the wallet.
     */
    public function movements()
    {
        return $this->hasMany(WalletMovement::class);
    }
}
