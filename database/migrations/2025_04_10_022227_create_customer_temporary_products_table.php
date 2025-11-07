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
        Schema::create('customer_temporary_products', function (Blueprint $table) {
            $table->id();
            $table->string('orderCode');
            $table->date('transaction_created');
            $table->rawIndex('orderCode,companyId,transaction_created', 'index_orcitc_customer_temporary_product');
            $table->bigInteger('companyId')->unsigned()->nullable(true);
            $table->bigInteger('userId')->unsigned();
            $table->bigInteger('customerCompanyGoodId')
                ->unsigned()
                ->nullable(true);
            $table->string('name')->nullable(true);
            $table->enum('status', ['draft', 'archive', 'publish'])->nullable(true);
            $table->string('picture')->nullable(true);
            $table->integer('stock')->nullable(true);
            $table->integer('stock_reference')->nullable(true);
            $table->decimal('price', 12, 2)->nullable(true);
            $table->decimal('buyPrice', 12, 2)->nullable(true);
            $table->bigInteger('unitId')
                ->unsigned()
                ->nullable(true);
            $table->bigInteger('typeId')
                ->unsigned()
                ->nullable(false);
            $table->foreign('typeId')
                ->on('app_product_types')
                ->references('id')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->boolean('accepted')->default(false);
            $table->bigInteger('accepted_by')->unsigned()->nullable(true);
            $table->foreign('unitId')
                ->on('app_good_units')
                ->references('id')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->foreign('customerCompanyGoodId')
                ->on('customer_company_goods')
                ->references('id')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->foreign('companyId')
                ->on('customer_companies')
                ->references('id')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->foreign('userId')
                ->on('users')
                ->references('id')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->foreign('accepted_by')
                ->on('users')
                ->references('id')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->unique(['orderCode', 'name'], 'order_name');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_temporary_products');
    }
};
