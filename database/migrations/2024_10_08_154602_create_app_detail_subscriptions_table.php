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
            $table->string('text_feature')->nullable(false);
            $table->integer('amount')->nullable(true);
            $table->boolean('status')->nullable(true);
            $table->enum('category', ['file', 'logic', 'custom_menu', 'transaction', 'data', 'custom_report', 'full_access_report']);
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
