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
        Schema::create('task_item_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('task_item_id');
            $table->enum('action', ['INSERT', 'UPDATE', 'DELETE']);
            $table->json('old_data')->nullable();
            $table->json('new_data')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        DB::unprepared("
            CREATE TRIGGER trg_task_items_after_insert
            AFTER INSERT ON task_items
            FOR EACH ROW
            BEGIN
                INSERT INTO task_item_logs (task_item_id, action, new_data)
                VALUES (NEW.id, 'INSERT', JSON_OBJECT(
                    'id', NEW.id, 'task_id', NEW.task_id, 'title', NEW.title, 'description', NEW.description, 'is_done', NEW.is_done, 'completed_at', NEW.completed_at, 'evidence', NEW.evidence, 'assigned_to_user_id', NEW.assigned_to_user_id, 'assigned_to_role_id', NEW.assigned_to_role_id, 'company_id', NEW.company_id, 'created_by', NEW.created_by, 'deleted_by', NEW.deleted_by, 'created_at', NEW.created_at, 'updated_at', NEW.updated_at, 'deleted_at', NEW.deleted_at
                ));
            END
        ");

        DB::unprepared("
            CREATE TRIGGER trg_task_items_after_update
            AFTER UPDATE ON task_items
            FOR EACH ROW
            BEGIN
                INSERT INTO task_item_logs (task_item_id, action, old_data, new_data)
                VALUES (NEW.id, 'UPDATE', JSON_OBJECT(
                    'id', OLD.id, 'task_id', OLD.task_id, 'title', OLD.title, 'description', OLD.description, 'is_done', OLD.is_done, 'completed_at', OLD.completed_at, 'evidence', OLD.evidence, 'assigned_to_user_id', OLD.assigned_to_user_id, 'assigned_to_role_id', OLD.assigned_to_role_id, 'company_id', OLD.company_id, 'created_by', OLD.created_by, 'deleted_by', OLD.deleted_by, 'created_at', OLD.created_at, 'updated_at', OLD.updated_at, 'deleted_at', OLD.deleted_at
                ), JSON_OBJECT(
                    'id', NEW.id, 'task_id', NEW.task_id, 'title', NEW.title, 'description', NEW.description, 'is_done', NEW.is_done, 'completed_at', NEW.completed_at, 'evidence', NEW.evidence, 'assigned_to_user_id', NEW.assigned_to_user_id, 'assigned_to_role_id', NEW.assigned_to_role_id, 'company_id', NEW.company_id, 'created_by', NEW.created_by, 'deleted_by', NEW.deleted_by, 'created_at', NEW.created_at, 'updated_at', NEW.updated_at, 'deleted_at', NEW.deleted_at
                ));
            END
        ");

        DB::unprepared("
            CREATE TRIGGER trg_task_items_after_delete
            AFTER DELETE ON task_items
            FOR EACH ROW
            BEGIN
                INSERT INTO task_item_logs (task_item_id, action, old_data)
                VALUES (OLD.id, 'DELETE', JSON_OBJECT(
                    'id', OLD.id, 'task_id', OLD.task_id, 'title', OLD.title, 'description', OLD.description, 'is_done', OLD.is_done, 'completed_at', OLD.completed_at, 'evidence', OLD.evidence, 'assigned_to_user_id', OLD.assigned_to_user_id, 'assigned_to_role_id', OLD.assigned_to_role_id, 'company_id', OLD.company_id, 'created_by', OLD.created_by, 'deleted_by', OLD.deleted_by, 'created_at', OLD.created_at, 'updated_at', OLD.updated_at, 'deleted_at', OLD.deleted_at
                ));
            END
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS trg_task_items_after_insert');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_task_items_after_update');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_task_items_after_delete');
        Schema::dropIfExists('task_item_logs');
    }
};
