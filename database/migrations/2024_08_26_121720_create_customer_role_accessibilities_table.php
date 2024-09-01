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
        Schema::create('customer_role_accessibilities', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('roleId')->unsigned();
            $table->foreign('roleId')
                ->on('customer_roles')
                ->references('id')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->bigInteger('menuId')->unsigned();
            $table->foreign('menuId')
                ->on('app_menus')
                ->references('id')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->bigInteger('userId')->unsigned();
            $table->foreign('userId')
                ->on('users')
                ->references('id')
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
        Schema::dropIfExists('customer_role_accessibilities');
    }
};
