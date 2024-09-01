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
        Schema::create('role_menus', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('roleId')
                ->unsigned();
            $table->bigInteger('menuId')
                ->unsigned();
            $table->foreign('roleId')
                ->references('id')
                ->on('customer_roles')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreign('menuId')
                ->references('id')
                ->on('app_menus')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_menus');
    }
};
