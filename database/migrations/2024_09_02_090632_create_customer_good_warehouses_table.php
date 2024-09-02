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
        Schema::create('customer_good_warehouses', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('warehouseId')->unsigned();
            $table->foreign('warehouseId')
                ->references('id')
                ->on('customer_company_warehouses')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->bigInteger('rackId')->unsigned();
            $table->foreign('rackId')
                ->references('id')
                ->on('customer_warehouse_racks')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->bigInteger('goodId')->unsigned();
            $table->foreign('goodId')
                ->references('id')
                ->on('customer_company_goods')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_good_warehouses');
    }
};
