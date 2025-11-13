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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('master_task_id')->unsigned()->nullable();
            $table->string('title');
            $table->string('description');
            $table->bigInteger('assigned_to_user_id')->unsigned()->nullable();
            $table->foreign('assigned_to_user_id')->references('id')->on('users')->cascadeOnUpdate()->cascadeOnDelete();
            $table->bigInteger('assigned_to_role_id')->unsigned()->nullable();
            $table->foreign('assigned_to_role_id')->references('id')->on('users')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('status');
            $table->smallInteger('percentage')->default(0);
            $table->timestamp('due_date')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->bigInteger('scope_id')->unsigned();
            $table->foreign('scope_id')->references('id')->on('scopes')->cascadeOnUpdate()->cascadeOnDelete();
            $table->bigInteger('company_id')->unsigned();
            $table->foreign('company_id')->references('id')->on('companies')->cascadeOnUpdate()->cascadeOnDelete();
            $table->bigInteger('created_by')->unsigned();
            $table->foreign('created_by')->references('id')->on('users')->cascadeOnUpdate()->cascadeOnDelete();
            $table->bigInteger('deleted_by')->unsigned()->nullable();
            $table->foreign('deleted_by')->references('id')->on('users')->cascadeOnUpdate()->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
