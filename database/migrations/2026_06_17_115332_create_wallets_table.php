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
        Schema::create('wallets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete()->index();
            $table->string('name')->comment('Name of the investment wallet');
            // Currency for display purposes (can be changed at will, and all investments will be shown in this currency - converted using the current exchange rate, but it does not affect fiscal reports or tax calculations)
            $table->string('view_currency', 3)->comment('Visible currency code (all investments will be shown in this currency, can be changed at will) (e.g., USD, BRL)');
            // Currency for fiscal reports and tax calculations (Its rigid, and commands how fx_rate will be converted for tax purposes, and how the fiscal report will be generated)
            $table->string('fiscal_residence_currency', 3)->comment('Currency code for fiscal residence (used for tax calculations) (e.g., USD, BRL)');
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['user_id', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallets');
    }
};
