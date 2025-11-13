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
        Schema::create('discount_usage', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('discount_id')->unsigned();
            $table->foreign('discount_id')
                ->on('discounts')
                ->references('id')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->bigInteger('discount_code_id')->unsigned();
            $table->foreign('discount_code_id')
                ->on('discount_codes')
                ->references('id')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->bigInteger('transaction_id')->unsigned();
            $table->foreign('transaction_id')
                ->on('transactions')
                ->references('id')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->decimal('discount_value', 16, 2);
            $table->bigInteger('company_id')->unsigned();
            $table->foreign('company_id')
                ->on('companies')
                ->references('id')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->bigInteger('created_by')
                ->unsigned();
            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();
            $table->bigInteger('deleted_by')->unsigned()->nullable();
            $table->foreign('deleted_by')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();
            $table->timestamps('applied_at');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discount_usage');
    }
};
