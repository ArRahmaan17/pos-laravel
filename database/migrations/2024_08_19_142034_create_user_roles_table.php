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
        Schema::create('user_roles', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('userId')->unsigned();
            $table->foreign('userId')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->bigInteger('roleId')->unsigned();
            $table->foreign('roleId')
                ->references('id')
                ->on('app_roles')
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
        Schema::dropIfExists('user_roles');
    }
};
