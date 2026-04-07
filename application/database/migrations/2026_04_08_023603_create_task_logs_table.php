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
        Schema::create('task_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('task_id');
            $table->enum('action', ['INSERT', 'UPDATE', 'DELETE']);
            $table->json('old_data')->nullable();
            $table->json('new_data')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        DB::unprepared("
            CREATE TRIGGER trg_tasks_after_insert
            AFTER INSERT ON tasks
            FOR EACH ROW
            BEGIN
                INSERT INTO task_logs (task_id, action, new_data)
                VALUES (NEW.id, 'INSERT', JSON_OBJECT(
                    'id', NEW.id, 'master_task_id', NEW.master_task_id, 'name', NEW.name, 'description', NEW.description, 'assigned_to_user_id', NEW.assigned_to_user_id, 'assigned_to_role_id', NEW.assigned_to_role_id, 'status', NEW.status, 'percentage', NEW.percentage, 'due_date', NEW.due_date, 'completed_at', NEW.completed_at, 'scope_id', NEW.scope_id, 'company_id', NEW.company_id, 'created_by', NEW.created_by, 'deleted_by', NEW.deleted_by, 'created_at', NEW.created_at, 'updated_at', NEW.updated_at, 'deleted_at', NEW.deleted_at
                ));
            END
        ");

        DB::unprepared("
            CREATE TRIGGER trg_tasks_after_update
            AFTER UPDATE ON tasks
            FOR EACH ROW
            BEGIN
                INSERT INTO task_logs (task_id, action, old_data, new_data)
                VALUES (NEW.id, 'UPDATE', JSON_OBJECT(
                    'id', OLD.id, 'master_task_id', OLD.master_task_id, 'name', OLD.name, 'description', OLD.description, 'assigned_to_user_id', OLD.assigned_to_user_id, 'assigned_to_role_id', OLD.assigned_to_role_id, 'status', OLD.status, 'percentage', OLD.percentage, 'due_date', OLD.due_date, 'completed_at', OLD.completed_at, 'scope_id', OLD.scope_id, 'company_id', OLD.company_id, 'created_by', OLD.created_by, 'deleted_by', OLD.deleted_by, 'created_at', OLD.created_at, 'updated_at', OLD.updated_at, 'deleted_at', OLD.deleted_at
                ), JSON_OBJECT(
                    'id', NEW.id, 'master_task_id', NEW.master_task_id, 'name', NEW.name, 'description', NEW.description, 'assigned_to_user_id', NEW.assigned_to_user_id, 'assigned_to_role_id', NEW.assigned_to_role_id, 'status', NEW.status, 'percentage', NEW.percentage, 'due_date', NEW.due_date, 'completed_at', NEW.completed_at, 'scope_id', NEW.scope_id, 'company_id', NEW.company_id, 'created_by', NEW.created_by, 'deleted_by', NEW.deleted_by, 'created_at', NEW.created_at, 'updated_at', NEW.updated_at, 'deleted_at', NEW.deleted_at
                ));
            END
        ");

        DB::unprepared("
            CREATE TRIGGER trg_tasks_after_delete
            AFTER DELETE ON tasks
            FOR EACH ROW
            BEGIN
                INSERT INTO task_logs (task_id, action, old_data)
                VALUES (OLD.id, 'DELETE', JSON_OBJECT(
                    'id', OLD.id, 'master_task_id', OLD.master_task_id, 'name', OLD.name, 'description', OLD.description, 'assigned_to_user_id', OLD.assigned_to_user_id, 'assigned_to_role_id', OLD.assigned_to_role_id, 'status', OLD.status, 'percentage', OLD.percentage, 'due_date', OLD.due_date, 'completed_at', OLD.completed_at, 'scope_id', OLD.scope_id, 'company_id', OLD.company_id, 'created_by', OLD.created_by, 'deleted_by', OLD.deleted_by, 'created_at', OLD.created_at, 'updated_at', OLD.updated_at, 'deleted_at', OLD.deleted_at
                ));
            END
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("DROP TRIGGER IF EXISTS trg_tasks_after_insert");
        DB::unprepared("DROP TRIGGER IF EXISTS trg_tasks_after_update");
        DB::unprepared("DROP TRIGGER IF EXISTS trg_tasks_after_delete");
        Schema::dropIfExists('task_logs');
    }
};
