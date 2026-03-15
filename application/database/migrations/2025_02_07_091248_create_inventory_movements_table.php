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
        Schema::create('inventory_movements', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('transaction_item_id')->unsigned();
            $table->foreign('transaction_item_id')
                ->references('id')
                ->on('transaction_items')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->bigInteger('warehouse_id')->unsigned();
            $table->foreign('warehouse_id')
                ->references('id')
                ->on('warehouses')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->bigInteger('product_id')->unsigned();
            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->bigInteger('inventory_movement_type_id')->unsigned();
            $table->foreign('inventory_movement_type_id')
                ->references('id')
                ->on('inventory_movement_types')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->integer('quantity_change');
            $table->string('remarks');
            $table->bigInteger('company_id')->unsigned();
            $table->foreign('company_id')
                ->references('id')
                ->on('companies')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->bigInteger('created_by')->unsigned();
            $table->foreign('created_by')->on('users')->references('id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->bigInteger('updated_by')->unsigned()->nullable();
            $table->foreign('updated_by')->on('users')->references('id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->bigInteger('deleted_by')->unsigned()->nullable();
            $table->foreign('deleted_by')->on('users')->references('id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_movements');
    }
};
