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
        // Schema::create('user_subscriptions', function (Blueprint $table) {
        //     $table->id();
        //     $table->bigInteger('user_id')->unsigned();
        //     $table->bigInteger('subscriptionId')->unsigned();
        //     $table->date('startDate');
        //     $table->date('endDate');
        //     $table->enum('status', ['trail', 'active', 'canceled', 'expired']);
        //     $table->foreign('subscriptionId')
        //         ->on('app_subscriptions')
        //         ->references('id')
        //         ->cascadeOnDelete()
        //         ->cascadeOnUpdate();
        //     $table->foreign('user_id')
        //         ->on('users')
        //         ->references('id')
        //         ->cascadeOnDelete()
        //         ->cascadeOnUpdate();
        //     $table->timestamps();
        //     $table->softDeletes();
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::dropIfExists('user_subcriptions');
    }
};
