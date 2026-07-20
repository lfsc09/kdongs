<?php

namespace Database\Seeders;

use App\Enums\Investment\CountryCode;
use App\Enums\Investment\CurrencyCode;
use App\Models\Investment\FixedIncomeBond;
use App\Models\Investment\Wallet;
use App\Models\Investment\WalletMovement;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()
            ->has(
                Wallet::factory()
                    ->count(1)
                    ->ofFiscalResidenceCurrency(CurrencyCode::BRL)
                    ->has(
                        WalletMovement::factory()
                            ->count(6)
                            ->asDeposit()
                            ->ofSourceCurrency(CurrencyCode::BRL),
                        'movements'
                    )
                    ->has(
                        WalletMovement::factory()
                            ->count(2)
                            ->asWithdrawal()
                            ->ofSourceCurrency(CurrencyCode::BRL),
                        'movements'
                    )
                    ->has(
                        FixedIncomeBond::factory()
                            ->count(5)
                            ->ofCountryCode(CountryCode::BR),
                        'bonds'
                    )
            )
            ->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => Hash::make('123456'),
            ]);
    }
}
