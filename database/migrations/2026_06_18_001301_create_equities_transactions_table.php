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
        Schema::create('equities_transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('equity_position_id')->constrained('equities_positions')->cascadeOnUpdate()->cascadeOnDelete()->index();
            $table->string('transaction_type')->comment('Type of transaction (e.g., buy, sell, transfer_in, transfer_out, etc.)');
            $table->dateTime('date_utc')->comment('Date and time of the transaction in UTC');
            // Quantity and price for buy/sell transactions (always positive values)
            $table->decimal('shares_amount', 20, 6)->nullable()->comment('Amount of shares involved in the transaction');
            $table->decimal('price_quote', 20, 6)->nullable()->comment('Price per share for the transaction');
            // Corporate events in cash (e.g Provents) or multiplier events (e.g. Stock Split)
            $table->decimal('cash_amount', 20, 6)->nullable()->comment('Total cash amount involved in the transaction (e.g., dividends, cash proceeds)');
            $table->decimal('factor', 20, 6)->nullable()->comment('Factor for corporate events (e.g., stock split ratio)');
            // Costs (mostly negative values)
            $table->decimal('taxes', 20, 6)->nullable()->comment('Total taxes applied to the transaction');
            $table->decimal('fees', 20, 6)->nullable()->comment('Total fees applied to the transaction');
            // Speficic fields for dividends
            $table->dateTime('date_com_utc')->nullable()->comment('Date and time of the ex-dividend date in UTC');
            $table->dateTime('date_payment_utc')->nullable()->comment('Date and time of the payment date in UTC');
            // Specific fields for custody transfers
            $table->string('other_institution')->nullable()->comment('Institution involved in the custody transfer (e.g., receiving or sending institution)');
            // Foreign exchange rate to the residency currency at the time of the transaction
            $table->decimal('fx_rate_to_residency', 20, 6)->default(1)->comment('Foreign exchange rate to the residency currency at the time of the transaction');
            $table->string('details')->nullable()->comment('Optional details of the transaction');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equities_transactions');
    }
};
