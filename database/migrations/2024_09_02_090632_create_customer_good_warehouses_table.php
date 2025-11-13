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
        // Schema::create('customer_good_warehouses', function (Blueprint $table) {
        //     $table->id();
        //     $table->bigInteger('warehouse_id')->unsigned();
        //     $table->foreign('warehouse_id')
        //         ->references('id')
        //         ->on('warehouses')
        //         ->cascadeOnDelete()
        //         ->cascadeOnUpdate();
        //     $table->bigInteger('rackId')->unsigned();
        //     $table->foreign('rackId')
        //         ->references('id')
        //         ->on('warehouse_inventories')
        //         ->cascadeOnDelete()
        //         ->cascadeOnUpdate();
        //     $table->bigInteger('goodId')->unsigned();
        //     $table->foreign('goodId')
        //         ->references('id')
        //         ->on('products')
        //         ->cascadeOnDelete()
        //         ->cascadeOnUpdate();
        //     $table->timestamps();
        //     $table->softDeletes();
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::dropIfExists('customer_good_warehouses');
    }
};
