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
        Schema::create('fixed_income_bonds', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('wallet_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete()->index();
            $table->string('done_state')->default('active')->comment('State of the bond (e.g., active, done, transferred)');
            // Bond location
            $table->string('currency', 3)->comment('Currency of the bond (e.g., USD, BRL)');
            $table->string('country_code', 2)->comment('Country code of the bond (e.g., US, BR)');
            $table->string('name')->comment('Name of the bond');
            // Bond classification
            $table->string('asset_class')->comment('Asset class of the bond (e.g., public [government], private [corporate])');
            $table->string('bond_type')->comment('Type of bond (e.g., brl_cdb, brl_lca, brl_lci, brl_debenture, brl_treasury_ipca, etc.)');
            // Institutions
            $table->string('holder_institution')->comment('Institution holding the bond');
            $table->string('issuer_institution')->comment('Institution issuing the bond');
            // Profitability rules
            $table->string('interest_type')->comment('Type of interest (e.g., fixed, floating)');
            $table->string('index_type')->comment('Type of index (e.g., brl_cdi, brl_ipca, brl_selic, usd_effr, etc.)');
            // Bond dates
            $table->dateTime('maturity_date_utc')->comment('Maturity date of the bond in UTC');
            $table->dateTime('enter_date_utc')->comment('Date when the bond was entered in UTC');
            $table->dateTime('exit_date_utc')->nullable()->comment('Date when the bond was exited in UTC, if applicable');
            // Consolidated fields
            $table->decimal('total_shares_amount', 20, 6)->default(0)->comment('Total amount of shares for the bond');
            $table->decimal('total_input_amount', 20, 6)->default(0)->comment('Total input amount for the bond');
            $table->decimal('current_gross_amount', 20, 6)->default(0)->comment('Current gross amount of the bond');
            $table->decimal('current_net_amount', 20, 6)->default(0)->comment('Current net amount of the bond');
            $table->string('details')->nullable()->comment('Optional details of the bond');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fixed_income_bonds');
    }
};
