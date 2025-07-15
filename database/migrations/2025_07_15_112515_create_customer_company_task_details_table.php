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
        Schema::create('customer_company_task_details', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('taskId')->unsigned();
            $table->bigInteger('masterId')->unsigned();
            $table->foreign('taskId')->references('id')->on('customer_company_tasks')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('masterId')->references('id')->on('customer_company_master_tasks')->cascadeOnUpdate()->cascadeOnDelete();
            $table->timestamp('start_at');
            $table->timestamp('end_at')->nullable();
            $table->boolean('status')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_company_task_details');
    }
};
