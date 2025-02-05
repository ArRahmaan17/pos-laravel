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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('username')->nullable(false)->unique();
            $table->string('email')->nullable(true)->unique();
            $table->string('phone_number')->nullable(true)->unique();
            $table->string('password')->nullable(false);
            $table->string('profile_picture')->nullable(true);
            $table->string('affiliate_code')->nullable(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
