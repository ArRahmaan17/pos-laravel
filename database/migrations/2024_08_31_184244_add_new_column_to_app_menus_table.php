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
        Schema::table('app_menus', function (Blueprint $table) {
            $table->integer('place')->default(0)->comment('0 as sidebar and 1 as profile');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('app_menus', function (Blueprint $table) {
            //
        });
    }
};
