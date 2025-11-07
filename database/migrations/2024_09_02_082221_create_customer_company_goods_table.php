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
        Schema::create('customer_company_goods', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable(false);
            $table->string('picture')->nullable(false);
            $table->integer('stock')->nullable(false);
            $table->decimal('price', 12, 2)->nullable(false);
            $table->decimal('buyPrice', 12, 2)->nullable(false);
            $table->bigInteger('unitId')
                ->unsigned()
                ->nullable(false);
            $table->foreign('unitId')
                ->on('app_good_units')
                ->references('id')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->bigInteger('typeId')
                ->unsigned()
                ->nullable(false);
            $table->foreign('typeId')
                ->on('app_product_types')
                ->references('id')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->bigInteger('companyId')->unsigned();
            $table->foreign('companyId')
                ->on('customer_companies')
                ->references('id')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->index(['name', 'typeId', 'unitId', 'companyId']);
            $table->enum('status', ['draft', 'archive', 'publish']);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_company_goods');
    }
};
