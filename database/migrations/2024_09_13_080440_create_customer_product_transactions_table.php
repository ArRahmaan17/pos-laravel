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
        Schema::create('customer_product_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('orderCode');
            $table->bigInteger('userId')->unsigned();
            $table->foreign('userId')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->bigInteger('companyId')->unsigned();
            $table->foreign('companyId')
                ->references('id')
                ->on('customer_companies')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->decimal('total', 16, 2);
            $table->decimal('discount', 16, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_product_transactions');
    }
};
