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
        Schema::create('customer_company_stocktakings', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('userId')->unsigned();
            $table->bigInteger('goodId')->unsigned();
            $table->bigInteger('companyId')->unsigned();
            $table->integer('expect_stock');
            $table->integer('real_stock');
            $table->smallInteger('status')->default(0);
            $table->foreign('userId')->references('id')->on('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('goodId')->references('id')->on('customer_company_goods')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('companyId')->references('id')->on('customer_companies')->cascadeOnDelete()->cascadeOnUpdate();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_company_stocktakings');
    }
};
