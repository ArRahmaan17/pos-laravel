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
        // Schema::create('transaction_items', function (Blueprint $table) {
        //     $table->id();
        //     $table->string('orderCode');
        //     $table->bigInteger('goodId')->unsigned();
        //     $table->foreign('goodId')
        //         ->references('id')
        //         ->on('products')
        //         ->cascadeOnDelete()
        //         ->cascadeOnUpdate();
        //     $table->integer('quantity');
        //     $table->integer('stock_reference');
        //     $table->decimal('price', 16, 2)->nullable();
        //     $table->decimal('total', 16, 2)->nullable()->generatedAs('(quantity * price)');
        //     $table->timestamps();
        //     $table->softDeletes();
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::dropIfExists('transaction_items');
    }
};
