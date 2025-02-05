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
        Schema::create('customer_detail_product_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('orderCode');
            $table->bigInteger('goodId')->unsigned();
            $table->foreign('goodId')
                ->references('id')
                ->on('customer_company_goods')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->integer('quantity');
            $table->decimal('price', 16, 2)->nullable(true);
            $table->decimal('total', 16, 2)->nullable(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_detail_product_transactions');
    }
};
