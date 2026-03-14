<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SimpleTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function application_returns_200_for_login_page()
    {
        $response = $this->get('/auth/login');

        $response->assertStatus(200);
    }

    /** @test */
    public function application_returns_200_for_registration_page()
    {
        $response = $this->get('/auth/registration');

        $response->assertStatus(200);
    }

    /** @test */
    public function home_page_redirects_unauthenticated_users()
    {
        $response = $this->get('/');

        $response->assertRedirect('/auth/login');
    }

    /** @test */
    public function api_company_types_endpoint_is_accessible()
    {
        $response = $this->getJson('/api/company-types');

        $response->assertStatus(200);
    }

    /** @test */
    public function protected_api_endpoints_require_authentication()
    {
        $response = $this->getJson('/api/me');

        $response->assertStatus(401);
    }

    /** @test */
    public function database_connection_works()
    {
        $this->assertDatabaseMissing('users', [
            'email' => 'nonexistent@example.com',
        ]);
    }

    /** @test */
    public function session_works_correctly()
    {
        session(['test_key' => 'test_value']);

        $this->assertEquals('test_value', session('test_key'));
    }

    /** @test */
    public function application_environment_is_setup()
    {
        $this->assertNotNull(config('app.name'));
        $this->assertNotNull(config('app.env'));
        $this->assertNotNull(config('database.default'));
    }

    /** @test */
    public function csrf_protection_is_enabled()
    {
        $response = $this->post('/auth/login');

        $response->assertStatus(419); // CSRF token mismatch
    }

    /** @test */
    public function error_pages_are_accessible()
    {
        $response = $this->get('/nonexistent-page');

        $response->assertStatus(404);
    }

    /** @test */
    public function select_company_page_requires_authentication()
    {
        $response = $this->get('/select-company');

        $response->assertRedirect('/auth/login');
    }

    /** @test */
    public function middleware_stack_is_working()
    {
        $response = $this->get('/man/customer-company');

        $response->assertRedirect('/auth/login');
    }

    /** @test */
    public function api_routes_are_properly_configured()
    {
        $response = $this->getJson('/api/check-available-user');

        $response->assertStatus(422); // Validation error for missing username parameter
    }

    /** @test */
    public function web_routes_are_properly_configured()
    {
        $response = $this->get('/auth/request-access-pin');

        $response->assertStatus(200);
    }
}
