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
        Schema::create('role_scope_inharitances', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('parent_scope_id')->unsigned();
            $table->bigInteger('child_scope_id')->unsigned();
            $table->smallInteger('can_user_create')->default(0);
            $table->smallInteger('can_org_create')->default(1);
            $table->foreign('parent_scope_id')->references('id')->on('scopes')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('child_scope_id')->references('id')->on('scopes')->cascadeOnDelete()->cascadeOnUpdate();
            $table->unique(['parent_scope_id', 'child_scope_id']);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_scope_inharitances');
    }
};
