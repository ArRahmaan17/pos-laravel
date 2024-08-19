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
        Schema::create('customer_roles', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('userId')->comment('customer manager id')->nullable(false)->unsigned();
            $table->foreign('userId')
                ->references('id')
                ->on('users')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->string('name')->nullable(false);
            $table->string('description')->nullable(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_roles');
    }
};
