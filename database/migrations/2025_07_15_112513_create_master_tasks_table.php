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
        Schema::create('master_tasks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description');
            $table->integer('recurrence_type')->default(1);
            $table->integer('recurrence_interval')->nullable();
            $table->timestamp('next_run_at')->nullable();
            $table->timestamp('last_run_at')->nullable();
            $table->smallInteger('is_active')->default(0);
            $table->bigInteger('role_id')->unsigned();
            $table->foreign('role_id')->references('id')->on('roles')->cascadeOnUpdate()->cascadeOnDelete();
            $table->bigInteger('scope_id')->unsigned();
            $table->foreign('scope_id')->references('id')->on('scope')->cascadeOnUpdate()->cascadeOnDelete();
            $table->bigInteger('company_id')->unsigned();
            $table->foreign('company_id')->references('id')->on('companies')->cascadeOnUpdate()->cascadeOnDelete();
            $table->bigInteger('created_by')->unsigned();
            $table->foreign('created_by')
                ->on('users')
                ->references('id')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->bigInteger('deleted_by')->nullable()->unsigned();
            $table->foreign('deleted_by')
                ->on('users')
                ->references('id')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_tasks');
    }
};
