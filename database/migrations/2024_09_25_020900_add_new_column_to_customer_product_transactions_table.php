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
        Schema::table('customer_product_transactions', function (Blueprint $table) {
            $table->bigInteger('discountId')->unsigned();
            $table->foreign('discountId')
                ->references('id')
                ->on('customer_company_discounts')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_product_transactions', function (Blueprint $table) {
            $table->dropColumn('discountId');
            $table->dropForeign('discountId');
        });
    }
};
