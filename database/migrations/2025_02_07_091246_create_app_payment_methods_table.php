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
        Schema::create('app_payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('icon');
            $table->text('description');
            $table->boolean('tax_coverage')->default(false);
            $table->integer('tax')->default(11);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_payment_methods');
    }
};
