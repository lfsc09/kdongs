<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('equities_positions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('wallet_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete()->index();
            $table->string('done_state')->default('active')->comment('State of the position (e.g., active, done, transferred)');
            // Equity location
            $table->string('currency', 3)->comment('Currency of the equity (e.g., USD, BRL)');
            $table->string('country_code', 2)->comment('Country code of the equity (e.g., US, BR)');
            // Institution
            $table->string('holder_institution')->comment('Institution holding the equity position');
            $table->string('asset_ticker')->comment('Ticker symbol of the equity asset');
            $table->string('asset_type')->comment('Type of equity asset (e.g., stock, fii, etf, reit, etc.)');
            // Consolidated fields
            $table->decimal('total_shares_amount', 20, 6)->default(0)->comment('Total amount of shares for the equity position');
            $table->decimal('average_price', 20, 6)->default(0)->comment('Average price of the equity position');
            $table->timestamps();
            $table->unique(['wallet_id', 'asset_ticker', 'asset_type', 'holder_institution'], 'unique_equity_position');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equities_positions');
    }
};
