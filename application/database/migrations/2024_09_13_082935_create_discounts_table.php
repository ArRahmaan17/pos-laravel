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
        Schema::create('discounts', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('product_id')->unsigned()->nullable();
            $table->foreign('product_id')
                ->on('products')
                ->references('id')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->bigInteger('kit_product_id')->unsigned()->nullable();
            $table->foreign('kit_product_id')
                ->on('kit_products')
                ->references('id')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->bigInteger('discount_code_id')->unsigned();
            $table->foreign('discount_code_id')
                ->on('discount_codes')
                ->references('id')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->bigInteger('discount_type_id')->unsigned();
            $table->foreign('discount_type_id')
                ->on('discount_types')
                ->references('id')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->decimal('discount_value', 16, 2)->nullable();
            $table->integer('percentage')->nullable();
            $table->integer('max_usage')->nullable();
            $table->decimal('max_amount_discount', 16, 2)->nullable();
            $table->decimal('min_amount_price', 16, 2)->default(0);
            $table->timestampTz('expirated_at');
            $table->enum('status', ['archive', 'draft', 'publish'])->default('draft');
            $table->bigInteger('company_id')->unsigned();
            $table->foreign('company_id')
                ->on('companies')
                ->references('id')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->bigInteger('created_by')->unsigned();
            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();
            $table->bigInteger('deleted_by')->unsigned()->nullable();
            $table->foreign('deleted_by')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discounts');
    }
};
