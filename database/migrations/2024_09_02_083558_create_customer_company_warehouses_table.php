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
        Schema::create('customer_company_warehouses', function (Blueprint $table) {
            $table->id();
            $table->string('name')
                ->nullable(false)
                ->unique();
            $table->string('description')
                ->nullable(false);
            $table->bigInteger('companyId')
                ->unsigned()
                ->nullable(false);
            $table->foreign('companyId')
                ->references('id')
                ->on('customer_companies')
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
        Schema::dropIfExists('customer_company_warehouses');
    }
};
