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
        Schema::create('warehouse_transfers', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('from_warehouse_id')->unsigned();
            $table->foreign('from_warehouse_id')
                ->on('warehouses')
                ->references('id')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->bigInteger('to_warehouse_id')->unsigned();
            $table->foreign('to_warehouse_id')
                ->on('warehouses')
                ->references('id')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->bigInteger('company_id')->unsigned();
            $table->foreign('company_id')
                ->on('companies')
                ->references('id')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->integer('total_items');
            $table->string('status');
            $table->bigInteger('approved_by')->unsigned()->nullable();
            $table->foreign('approved_by')
                ->on('users')
                ->references('id')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->bigInteger('created_by')->unsigned();
            $table->foreign('created_by')
                ->on('users')
                ->references('id')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->bigInteger('deleted_by')->nullable()->unsigned();
            $table->foreign('deleted_by')
                ->on('users')
                ->references('id')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->timestamps('approved_at');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouse_transfers');
    }
};
