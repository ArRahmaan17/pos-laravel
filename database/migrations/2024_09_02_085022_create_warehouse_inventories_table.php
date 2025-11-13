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
        Schema::create('warehouse_inventories', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('warehouse_id')->unsigned();
            $table->foreign('warehouse_id')
                ->references('id')
                ->on('warehouses')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->bigInteger('product_id')
                ->unsigned();
            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->bigInteger('quantity');
            $table->bigInteger('created_by')
                ->unsigned();
            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouse_inventories');
    }
};
