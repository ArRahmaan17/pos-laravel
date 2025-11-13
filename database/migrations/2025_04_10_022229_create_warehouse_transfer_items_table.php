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
        Schema::create('warehouse_transfer_items', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('transfer_id')->unsigned();
            $table->foreign('transfer_id')
                ->on('warehouse_transfers')
                ->references('id')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->bigInteger('product_id')->unsigned();
            $table->foreign('product_id')
                ->on('products')
                ->references('id')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->integer('quantity');
            $table->string('status');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouse_transfer_items');
    }
};
