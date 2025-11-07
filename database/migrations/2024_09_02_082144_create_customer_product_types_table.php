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
        Schema::create('customer_product_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description');
            $table->bigInteger('businessId')->unsigned();
            $table->foreign('businessId')
                ->references('id')
                ->on('business_types')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->index(['name', 'businessId']);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_product_types');
    }
};
