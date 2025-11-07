<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();

        // Set up test database
        $this->setUpTestDatabase();
    }

    protected function setUpTestDatabase(): void
    {
        // Create test database file if using SQLite file
        if (config('database.default') === 'sqlite' && config('database.connections.sqlite.database') !== ':memory:') {
            $databasePath = config('database.connections.sqlite.database');
            if (! file_exists($databasePath)) {
                touch($databasePath);
            }
        }

        // Run migrations for testing
        try {
            Artisan::call('migrate:fresh', ['--env' => 'testing']);
        } catch (\Exception $e) {
            // If migrations fail, try to create tables manually
            $this->createBasicTables();
        }
    }

    protected function createBasicTables(): void
    {
        // Create basic tables needed for testing
        $schema = DB::connection()->getSchemaBuilder();

        if (! $schema->hasTable('users')) {
            $schema->create('users', function ($table) {
                $table->id();
                $table->string('name');
                $table->string('username')->unique();
                $table->string('email')->unique();
                $table->string('phone_number')->nullable();
                $table->timestamp('email_verified_at')->nullable();
                $table->string('password');
                $table->string('access_pin')->nullable();
                $table->string('affiliate_code')->nullable();
                $table->string('registration_link')->nullable();
                $table->string('profile_picture')->nullable();
                $table->enum('status', ['active', 'inactive'])->default('active');
                $table->timestamp('last_login_at')->nullable();
                $table->string('last_login_ip')->nullable();
                $table->string('last_login_user_agent')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->unsignedBigInteger('updated_by')->nullable();
                $table->rememberToken();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (! $schema->hasTable('app_roles')) {
            $schema->create('app_roles', function ($table) {
                $table->id();
                $table->string('name');
                $table->text('description')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (! $schema->hasTable('business_types')) {
            $schema->create('business_types', function ($table) {
                $table->id();
                $table->string('name');
                $table->text('description')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (! $schema->hasTable('customer_companies')) {
            $schema->create('customer_companies', function ($table) {
                $table->id();
                $table->string('name');
                $table->string('phone_number');
                $table->unsignedBigInteger('businessId');
                $table->unsignedBigInteger('userId');
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (! $schema->hasTable('user_roles')) {
            $schema->create('user_roles', function ($table) {
                $table->id();
                $table->unsignedBigInteger('userId');
                $table->unsignedBigInteger('roleId');
                $table->timestamps();
            });
        }

        if (! $schema->hasTable('app_menus')) {
            $schema->create('app_menus', function ($table) {
                $table->id();
                $table->string('name');
                $table->string('icon')->nullable();
                $table->string('url')->nullable();
                $table->unsignedBigInteger('parentId')->nullable();
                $table->integer('place')->default(0);
                $table->integer('order')->default(0);
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (! $schema->hasTable('app_subscriptions')) {
            $schema->create('app_subscriptions', function ($table) {
                $table->id();
                $table->string('name');
                $table->text('description')->nullable();
                $table->decimal('price', 10, 2)->default(0);
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (! $schema->hasTable('customer_roles')) {
            $schema->create('customer_roles', function ($table) {
                $table->id();
                $table->bigInteger('userId')->comment('customer manager id')->nullable(false)->unsigned();
                $table->string('name')->nullable(false);
                $table->string('description')->nullable(false);
                $table->enum('as_role', ['cashier', 'sales', 'admin', 'warehouse']);
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }
}
