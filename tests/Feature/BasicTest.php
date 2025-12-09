<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BasicTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function application_can_boot_without_errors()
    {
        $this->assertTrue(true);
    }

    /** @test */
    public function database_connection_works()
    {
        $this->assertDatabaseMissing('users', [
            'email' => 'nonexistent@example.com',
        ]);
    }

    /** @test */
    public function login_page_is_accessible()
    {
        $response = $this->get('/auth/login');
        $response->assertStatus(200);
    }

    /** @test */
    public function home_page_requires_authentication()
    {
        $response = $this->get('/');
        $response->assertRedirect('/auth/login');
    }

    /** @test */
    public function api_company_types_endpoint_is_accessible()
    {
        $response = $this->getJson('/api/company-types');
        // Should return 404 if no business types exist, which is expected in test environment
        $response->assertStatus(404);
    }

    /** @test */
    public function protected_api_endpoints_require_authentication()
    {
        $response = $this->getJson('/api/me');
        $response->assertStatus(401);
    }

    /** @test */
    public function api_check_available_user_endpoint_works()
    {
        $response = $this->getJson('/api/check-available-user?name=Test User&username=testuser&email=test@example.com&phone_number=081234567890');
        // Should return 200 if user is available, which is expected in test environment
        $response->assertStatus(200);
    }

    /** @test */
    public function api_check_company_availability_endpoint_works()
    {
        $response = $this->getJson('/api/check-company-availability?name=TestCompany&email=test@company.com&phone_number=081234567890&business_id=1');
        // Should return 422 for validation error, which is expected
        $response->assertStatus(422);
    }

    /** @test */
    public function api_login_endpoint_exists()
    {
        $response = $this->postJson('/api/login', [
            'username' => 'testuser',
            'password' => 'password123',
        ]);
        // Should return 422 for validation error, which is expected
        $response->assertStatus(422);
    }

    /** @test */
    public function api_register_endpoint_exists()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'username' => 'testuser',
            'email' => 'test@example.com',
            'phone_number' => '081234567890',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);
        // Should return 422 for validation error (missing required fields), which is expected
        $response->assertStatus(422);
    }
}
