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
        Schema::create('wallet_movements', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('wallet_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete()->index();
            $table->string('type')->comment('Type of movement (e.g., deposit, withdrawal)');
            $table->string('institution')->nullable()->comment('Institution where the movement occurred');
            $table->string('source_currency', 3)->comment('Source currency code of the movement');
            $table->decimal('source_amount', 20, 6)->comment('Amount in the source currency (only positive values, even for withdrawals)');
            $table->decimal('fx_gross_rate', 20, 6)->nullable()->comment('Gross exchange rate to convert the source currency (e.g. 5.100000 for BRL to USD)');
            $table->decimal('fx_fee_percentage', 20, 6)->nullable()->comment('Exchange operation total percentage fee (e.g. fee + iof) (fees increase the exchange rate)');
            $table->decimal('fx_net_rate', 20, 6)->nullable()->comment('Final exchange rate to convert the source currency (considering the gross rate and the operation fee)');
            $table->string('target_currency', 3)->comment('Target currency code to which the source currency was converted');
            $table->decimal('target_amount', 20, 6)->comment('Amount in the target currency (only positive values, even for withdrawals)');
            $table->text('details')->nullable()->comment('Additional details about the movement');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_movements');
    }
};
