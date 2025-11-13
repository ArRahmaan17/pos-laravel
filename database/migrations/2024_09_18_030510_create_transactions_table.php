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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('transaction_type_id')->unsigned();
            $table->foreign('transaction_type_id')
                ->references('id')
                ->on('transaction_types')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->bigInteger('warehouse_id')->unsigned();
            $table->foreign('warehouse_id')
                ->references('id')
                ->on('warehouses')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->bigInteger('company_id')->unsigned();
            $table->foreign('company_id')
                ->references('id')
                ->on('companies')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->bigInteger('related_transaction_id')->unsigned()->nullable();
            $table->decimal('total_amount', 16, 2);
            $table->decimal('total_discount', 16, 2);
            $table->text('notes');
            $table->string('status')->default('pending');
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
            $table->unique(['orderCode', 'company_id']);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
