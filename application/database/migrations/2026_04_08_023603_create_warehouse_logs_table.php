<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('warehouse_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('warehouse_id');
            $table->enum('action', ['INSERT', 'UPDATE', 'DELETE']);
            $table->json('old_data')->nullable();
            $table->json('new_data')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        DB::unprepared("
            CREATE TRIGGER trg_warehouses_after_insert
            AFTER INSERT ON warehouses
            FOR EACH ROW
            BEGIN
                INSERT INTO warehouse_logs (warehouse_id, action, new_data)
                VALUES (NEW.id, 'INSERT', JSON_OBJECT(
                    'id', NEW.id, 'name', NEW.name, 'description', NEW.description, 'company_id', NEW.company_id, 'created_by', NEW.created_by, 'created_at', NEW.created_at, 'updated_at', NEW.updated_at, 'deleted_at', NEW.deleted_at
                ));
            END
        ");

        DB::unprepared("
            CREATE TRIGGER trg_warehouses_after_update
            AFTER UPDATE ON warehouses
            FOR EACH ROW
            BEGIN
                INSERT INTO warehouse_logs (warehouse_id, action, old_data, new_data)
                VALUES (NEW.id, 'UPDATE', JSON_OBJECT(
                    'id', OLD.id, 'name', OLD.name, 'description', OLD.description, 'company_id', OLD.company_id, 'created_by', OLD.created_by, 'created_at', OLD.created_at, 'updated_at', OLD.updated_at, 'deleted_at', OLD.deleted_at
                ), JSON_OBJECT(
                    'id', NEW.id, 'name', NEW.name, 'description', NEW.description, 'company_id', NEW.company_id, 'created_by', NEW.created_by, 'created_at', NEW.created_at, 'updated_at', NEW.updated_at, 'deleted_at', NEW.deleted_at
                ));
            END
        ");

        DB::unprepared("
            CREATE TRIGGER trg_warehouses_after_delete
            AFTER DELETE ON warehouses
            FOR EACH ROW
            BEGIN
                INSERT INTO warehouse_logs (warehouse_id, action, old_data)
                VALUES (OLD.id, 'DELETE', JSON_OBJECT(
                    'id', OLD.id, 'name', OLD.name, 'description', OLD.description, 'company_id', OLD.company_id, 'created_by', OLD.created_by, 'created_at', OLD.created_at, 'updated_at', OLD.updated_at, 'deleted_at', OLD.deleted_at
                ));
            END
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("DROP TRIGGER IF EXISTS trg_warehouses_after_insert");
        DB::unprepared("DROP TRIGGER IF EXISTS trg_warehouses_after_update");
        DB::unprepared("DROP TRIGGER IF EXISTS trg_warehouses_after_delete");
        Schema::dropIfExists('warehouse_logs');
    }
};
