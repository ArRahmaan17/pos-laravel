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
        Schema::create('transaction_items', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('transaction_item_id')->unsigned();
            $table->foreign('transaction_item_id')
                ->references('id')
                ->on('transaction_items')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->bigInteger('discount_id')->unsigned();
            $table->foreign('discount_id')
                ->references('id')
                ->on('discounts')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->bigInteger('discount_code_id')->unsigned();
            $table->foreign('discount_code_id')
                ->references('id')
                ->on('discount_codes')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->decimal('discount_value', 16, 2);
            $table->bigInteger('created_by')->unsigned();
            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->bigInteger('deleted_by')->unsigned()->nullable();
            $table->foreign('deleted_by')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_items');
    }
};
