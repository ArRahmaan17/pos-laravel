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
        Schema::create('app_detail_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('subscriptionId')->unsigned();
            $table->foreign('subscriptionId')
                ->references('id')
                ->on('app_subscriptions')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->string('planFeature')->nullable(false);
            $table->boolean('status')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_detail_subscriptions');
    }
};
