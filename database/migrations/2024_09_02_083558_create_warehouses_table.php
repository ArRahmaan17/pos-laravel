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
        Schema::create('warehouses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description');
            $table->bigInteger('company_id')
                ->unsigned();
            $table->foreign('company_id')
                ->references('id')
                ->on('companies')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->bigInteger('created_by')
                ->unsigned();
            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['name', 'company_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warehouses');
    }
};
