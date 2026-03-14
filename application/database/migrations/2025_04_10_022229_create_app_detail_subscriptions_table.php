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
        // Schema::create('app_detail_subscriptions', function (Blueprint $table) {
        //     $table->id();
        //     $table->bigInteger('subscriptionId')->unsigned();
        //     $table->foreign('subscriptionId')
        //         ->references('id')
        //         ->on('app_subscriptions')
        //         ->onUpdate('cascade')
        //         ->onDelete('cascade');
        //     $table->bigInteger('featureId')->unsigned();
        //     $table->foreign('featureId')
        //         ->references('id')
        //         ->on('app_subscription_template_feature')
        //         ->onUpdate('cascade')
        //         ->onDelete('cascade');
        //     $table->integer('heap')->nullable();
        //     $table->boolean('condition')->nullable();
        //     $table->timestamps();
        //     $table->softDeletes();
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::dropIfExists('app_detail_subscriptions');
    }
};
