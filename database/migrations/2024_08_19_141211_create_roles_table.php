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
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code');
            $table->string('description');
            $table->bigInteger('parent_role_id')->unsigned();
            $table->bigInteger('scope_id')->unsigned();
            $table->foreign('scope_id')->references('id')->on('scopes')->cascadeOnDelete()->cascadeOnUpdate();
            $table->smallInteger('is_system')->default(0);
            $table->bigInteger('company_id')->unsigned();
            $table->foreign('company_id')->references('id')->on('companies')->cascadeOnDelete()->cascadeOnUpdate();
            $table->bigInteger('created_by')->unsigned()->nullable();
            $table->foreign('created_by')->on('users')->references('id')->cascadeOnDelete()->cascadeOnUpdate();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
