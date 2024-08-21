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
        Schema::create('user_companies', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('userId')->unsigned();
            $table->string('name')->nullable(false);
            $table->string('picture')->nullable(false);
            $table->string('phone_number')->nullable(false)->unique();
            $table->string('email')->nullable(false)->unique();
            $table->string('affiliate_code');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_companies');
    }
};
