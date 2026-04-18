<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('master_task_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('master_task_id');
            $table->enum('action', ['INSERT', 'UPDATE', 'DELETE']);
            $table->json('old_data')->nullable();
            $table->json('new_data')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        DB::unprepared("
            CREATE TRIGGER trg_master_tasks_after_insert
            AFTER INSERT ON master_tasks
            FOR EACH ROW
            BEGIN
                INSERT INTO master_task_logs (master_task_id, action, new_data)
                VALUES (NEW.id, 'INSERT', JSON_OBJECT(
                    'id', NEW.id,
                    'name', NEW.name,
                    'description', NEW.description,
                    'recurrence_type', NEW.recurrence_type,
                    'recurrence_interval', NEW.recurrence_interval,
                    'next_run_at', NEW.next_run_at,
                    'last_run_at', NEW.last_run_at,
                    'is_active', NEW.is_active,
                    'role_id', NEW.role_id,
                    'scope_id', NEW.scope_id,
                    'company_id', NEW.company_id,
                    'created_by', NEW.created_by,
                    'deleted_by', NEW.deleted_by
                ));
            END
        ");

        DB::unprepared("
            CREATE TRIGGER trg_master_tasks_after_update
            AFTER UPDATE ON master_tasks
            FOR EACH ROW
            BEGIN
                INSERT INTO master_task_logs (master_task_id, action, old_data, new_data)
                VALUES (NEW.id, 'UPDATE', JSON_OBJECT(
                    'id', OLD.id,
                    'name', OLD.name,
                    'description', OLD.description,
                    'recurrence_type', OLD.recurrence_type,
                    'recurrence_interval', OLD.recurrence_interval,
                    'next_run_at', OLD.next_run_at,
                    'last_run_at', OLD.last_run_at,
                    'is_active', OLD.is_active,
                    'role_id', OLD.role_id,
                    'scope_id', OLD.scope_id,
                    'company_id', OLD.company_id,
                    'created_by', OLD.created_by,
                    'deleted_by', OLD.deleted_by
                ), JSON_OBJECT(
                    'id', NEW.id,
                    'name', NEW.name,
                    'description', NEW.description,
                    'recurrence_type', NEW.recurrence_type,
                    'recurrence_interval', NEW.recurrence_interval,
                    'next_run_at', NEW.next_run_at,
                    'last_run_at', NEW.last_run_at,
                    'is_active', NEW.is_active,
                    'role_id', NEW.role_id,
                    'scope_id', NEW.scope_id,
                    'company_id', NEW.company_id,
                    'created_by', NEW.created_by,
                    'deleted_by', NEW.deleted_by
                ));
            END
        ");

        DB::unprepared("
            CREATE TRIGGER trg_master_tasks_after_delete
            AFTER DELETE ON master_tasks
            FOR EACH ROW
            BEGIN
                INSERT INTO master_task_logs (master_task_id, action, old_data)
                VALUES (OLD.id, 'DELETE', JSON_OBJECT(
                    'id', OLD.id,
                    'name', OLD.name,
                    'description', OLD.description,
                    'recurrence_type', OLD.recurrence_type,
                    'recurrence_interval', OLD.recurrence_interval,
                    'next_run_at', OLD.next_run_at,
                    'last_run_at', OLD.last_run_at,
                    'is_active', OLD.is_active,
                    'role_id', OLD.role_id,
                    'scope_id', OLD.scope_id,
                    'company_id', OLD.company_id,
                    'created_by', OLD.created_by,
                    'deleted_by', OLD.deleted_by
                ));
            END
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS trg_master_tasks_after_insert');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_master_tasks_after_update');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_master_tasks_after_delete');
        Schema::dropIfExists('master_task_logs');
    }
};
