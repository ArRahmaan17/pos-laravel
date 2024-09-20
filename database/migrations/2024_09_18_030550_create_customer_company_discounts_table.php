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
        Schema::create('customer_company_discounts', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('companyId')->unsigned();
            $table->string('code')->unique();
            $table->string('description');
            $table->integer('percentage');
            $table->decimal('maxTransactionDiscount', 16, 2)->nullable(true);
            $table->decimal('minTransactionPrice', 16, 2)->default(0);
            $table->enum('status', ['archive', 'draft', 'publish'])->default('draft');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_company_discounts');
    }
};
