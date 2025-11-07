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
        Schema::create('customer_company_master_tasks', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('companyId')->unsigned();
            $table->foreign('companyId')->references('id')->on('customer_companies')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('name');
            $table->string('description');
            $table->bigInteger('roleId')->unsigned();
            $table->foreign('roleId')->references('id')->on('customer_roles')->cascadeOnUpdate()->cascadeOnDelete();
            $table->enum('priority', ['P1', 'P2', 'P3', 'P4']);
            $table->boolean('repeateable')->default(0);
            $table->unique(['companyId', 'name']);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_company_master_tasks');
    }
};
