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
        Schema::create('product_category_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_category_id');
            $table->enum('action', ['INSERT', 'UPDATE', 'DELETE']);
            $table->json('old_data')->nullable();
            $table->json('new_data')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        DB::unprepared("
            CREATE TRIGGER trg_product_categories_after_insert
            AFTER INSERT ON product_categories
            FOR EACH ROW
            BEGIN
                INSERT INTO product_category_logs (product_category_id, action, new_data)
                VALUES (NEW.id, 'INSERT', JSON_OBJECT(
                    'id', NEW.id, 'name', NEW.name, 'description', NEW.description, 'created_by', NEW.created_by, 'updated_by', NEW.updated_by, 'deleted_by', NEW.deleted_by, 'company_id', NEW.company_id, 'created_at', NEW.created_at, 'updated_at', NEW.updated_at, 'deleted_at', NEW.deleted_at
                ));
            END
        ");

        DB::unprepared("
            CREATE TRIGGER trg_product_categories_after_update
            AFTER UPDATE ON product_categories
            FOR EACH ROW
            BEGIN
                INSERT INTO product_category_logs (product_category_id, action, old_data, new_data)
                VALUES (NEW.id, 'UPDATE', JSON_OBJECT(
                    'id', OLD.id, 'name', OLD.name, 'description', OLD.description, 'created_by', OLD.created_by, 'updated_by', OLD.updated_by, 'deleted_by', OLD.deleted_by, 'company_id', OLD.company_id, 'created_at', OLD.created_at, 'updated_at', OLD.updated_at, 'deleted_at', OLD.deleted_at
                ), JSON_OBJECT(
                    'id', NEW.id, 'name', NEW.name, 'description', NEW.description, 'created_by', NEW.created_by, 'updated_by', NEW.updated_by, 'deleted_by', NEW.deleted_by, 'company_id', NEW.company_id, 'created_at', NEW.created_at, 'updated_at', NEW.updated_at, 'deleted_at', NEW.deleted_at
                ));
            END
        ");

        DB::unprepared("
            CREATE TRIGGER trg_product_categories_after_delete
            AFTER DELETE ON product_categories
            FOR EACH ROW
            BEGIN
                INSERT INTO product_category_logs (product_category_id, action, old_data)
                VALUES (OLD.id, 'DELETE', JSON_OBJECT(
                    'id', OLD.id, 'name', OLD.name, 'description', OLD.description, 'created_by', OLD.created_by, 'updated_by', OLD.updated_by, 'deleted_by', OLD.deleted_by, 'company_id', OLD.company_id, 'created_at', OLD.created_at, 'updated_at', OLD.updated_at, 'deleted_at', OLD.deleted_at
                ));
            END
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS trg_product_categories_after_insert');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_product_categories_after_update');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_product_categories_after_delete');
        Schema::dropIfExists('product_category_logs');
    }
};
