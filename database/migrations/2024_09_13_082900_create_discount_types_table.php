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
        Schema::create('discount_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description');
            $table->bigInteger('company_id')->unsigned();
            $table->foreign('company_id')
                ->on('companies')
                ->references('id')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->bigInteger('created_by')
                ->unsigned();
            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();
            $table->bigInteger('deleted_by')->unsigned()->nullable();
            $table->foreign('deleted_by')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();
            $table->unique(['name', 'company_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discount_types');
    }
};
