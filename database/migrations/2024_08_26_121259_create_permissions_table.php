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
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('code')->unique();
            $table->string('resource');
            $table->string('description');
            $table->string('icon')->default('bx bxs-home');
            $table->integer('parent')->default(0);
            $table->smallInteger('min_scope_level')->default(1);
            $table->smallInteger('mandatory')->default(0);
            $table->integer('place')->default(0)->comment('0 as sidebar and 1 as profile');
            $table->bigInteger('created_by')->unsigned();
            $table->foreign('created_by')
                ->references('id')
                ->on('users')
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
        Schema::dropIfExists('permissions');
    }
};
