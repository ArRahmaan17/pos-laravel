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
        Schema::create('statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('description');
            $table->bigInteger('requester_id')->unsigned()->nullable();
            $table->foreign('requester_id')->on('users')->references('id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->bigInteger('accepter_id')->unsigned()->nullable();
            $table->foreign('accepter_id')->on('users')->references('id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->smallInteger('accepted')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('statuses');
    }
};
