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
        Schema::create('user_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->enum('action', ['INSERT', 'UPDATE', 'DELETE']);
            $table->json('old_data')->nullable();
            $table->json('new_data')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        DB::unprepared("
            CREATE TRIGGER trg_users_after_insert
            AFTER INSERT ON users
            FOR EACH ROW
            BEGIN
                INSERT INTO user_logs (user_id, action, new_data)
                VALUES (NEW.id, 'INSERT', JSON_OBJECT(
                    'id', NEW.id,
                    'name', NEW.name,
                    'username', NEW.username,
                    'email', NEW.email,
                    'phone_number', NEW.phone_number
                ));
            END
        ");

        DB::unprepared("
            CREATE TRIGGER trg_users_after_update
            AFTER UPDATE ON users
            FOR EACH ROW
            BEGIN
                INSERT INTO user_logs (user_id, action, old_data, new_data)
                VALUES (NEW.id, 'UPDATE', JSON_OBJECT(
                    'id', OLD.id,
                    'name', OLD.name,
                    'username', OLD.username,
                    'email', OLD.email,
                    'phone_number', OLD.phone_number
                ), JSON_OBJECT(
                    'id', NEW.id,
                    'name', NEW.name,
                    'username', NEW.username,
                    'email', NEW.email,
                    'phone_number', NEW.phone_number
                ));
            END
        ");

        DB::unprepared("
            CREATE TRIGGER trg_users_after_delete
            AFTER DELETE ON users
            FOR EACH ROW
            BEGIN
                INSERT INTO user_logs (user_id, action, old_data)
                VALUES (OLD.id, 'DELETE', JSON_OBJECT(
                    'id', OLD.id,
                    'name', OLD.name,
                    'username', OLD.username,
                    'email', OLD.email,
                    'phone_number', OLD.phone_number
                ));
            END
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS trg_users_after_insert');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_users_after_update');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_users_after_delete');
        Schema::dropIfExists('user_logs');
    }
};
