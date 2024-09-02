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
        Schema::create('customer_warehouse_racks', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('warehouseId')->unsigned();
            $table->foreign('warehouseId')
                ->references('id')
                ->on('customer_company_warehouses')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->string('name')->nullable(false);
            $table->string('description')->nullable(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_warehouse_racks');
    }
};
