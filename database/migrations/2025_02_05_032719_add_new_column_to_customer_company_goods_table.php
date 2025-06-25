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
            $table->addColumn('decimal', 'buyPrice', ['total' => 12, 'places' => 2]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_company_goods', function (Blueprint $table) {
            $table->dropColumn('buyPrice');
        });
    }
};
