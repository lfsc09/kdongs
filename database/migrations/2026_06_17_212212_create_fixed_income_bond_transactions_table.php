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
        Schema::create('fixed_income_bond_transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('fixed_income_bond_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete()->index();
            $table->string('transaction_type')->comment('Type of transaction (e.g., buy, sell)');
            $table->dateTime('date_utc')->comment('Date and time of the transaction in UTC');
            // Index value of the bond at the time of the transaction
            $table->decimal('index_value', 20, 6)->comment('Index value at the time of the transaction');
            // Unit price and shares amount
            $table->decimal('unit_price', 20, 6)->comment('Unit price of the bond at the time of the transaction');
            $table->decimal('shares_amount', 20, 6)->comment('Amount of shares involved in the transaction');
            // Costs (mostly negative values)
            $table->decimal('taxes', 20, 6)->comment('Total taxes applied to the transaction');
            $table->decimal('fees', 20, 6)->comment('Total fees applied to the transaction');
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
        Schema::dropIfExists('fixed_income_bond_transactions');
    }
};
