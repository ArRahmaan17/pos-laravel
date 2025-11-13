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
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('evindance');
            $table->bigInteger('transaction_id')->unsigned();
            $table->foreign('transaction_id')
                ->references('id')
                ->on('transactions')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->bigInteger('payment_id')->unsigned();
            $table->foreign('payment_id')
                ->references('id')
                ->on('payments')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->decimal('amount', 15, 2);
            $table->bigInteger('created_by')->unsigned();
            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->bigInteger('deleted_by')->unsigned()->nullable();
            $table->foreign('deleted_by')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->timestampTz('paid_at');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};
