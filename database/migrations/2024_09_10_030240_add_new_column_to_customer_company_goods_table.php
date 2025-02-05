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
        Schema::table('customer_company_goods', function (Blueprint $table) {
            $table->bigInteger('companyId')->unsigned();
            $table->foreign('companyId')
                ->on('customer_companies')
                ->references('id')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->enum('status', ['draft', 'archive', 'publish']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_company_goods', function (Blueprint $table) {
            $table->removeColumn('companyId');
            $table->removeColumn('status');
        });
    }
};
