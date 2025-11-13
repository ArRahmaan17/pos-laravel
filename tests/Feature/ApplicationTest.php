<?php

namespace Tests\Feature;

use App\Models\AppRole;
use App\Models\BusinessType;
use App\Models\CustomerCompany;
use App\Models\CustomerRole;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ApplicationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        // Create necessary roles and business types
        $this->developerRole = AppRole::factory()->create(['name' => 'Developer']);
        $this->managerRole = AppRole::factory()->create(['name' => 'Manager']);
        $this->customerRole = CustomerRole::factory()->create(['name' => 'Customer']);
        $this->businessType = BusinessType::factory()->create();
    }

    /** @test */
    public function login_view_returns_a_successful_response(): void
    {
        $response = $this->get('/auth/login');

        $response->assertStatus(200);
    }

    /** @test */
    public function registration_view_returns_a_successful_response(): void
    {
        $response = $this->get('/auth/registration');

        $response->assertStatus(200);
    }

    /** @test */
    public function home_page_requires_authentication(): void
    {
        $response = $this->get('/');

        $response->assertRedirect('/auth/login');
    }

    /** @test */
    public function authenticated_user_can_access_home_page(): void
    {
        $user = User::factory()->create();
        UserRole::factory()->create([
            'user_id' => $user->id,
            'role_id' => $this->developerRole->id,
        ]);

        $this->actingAs($user);

        session([
            'userLogged' => [
                'user' => $user->toArray(),
                'company' => ['id' => 1, 'name' => 'Test Company'],
            ],
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
    }

    /** @test */
    public function select_company_page_requires_authentication(): void
    {
        $response = $this->get('/select-company');

        $response->assertRedirect('/auth/login');
    }

    /** @test */
    public function authenticated_user_can_access_select_company_page(): void
    {
        $user = User::factory()->create();
        UserRole::factory()->create([
            'user_id' => $user->id,
            'role_id' => $this->developerRole->id,
        ]);

        $this->actingAs($user);

        $response = $this->get('/select-company');

        $response->assertStatus(200);
    }

    /** @test */
    public function api_routes_are_protected(): void
    {
        $response = $this->getJson('/api/me');

        $response->assertStatus(401);
    }

    /** @test */
    public function public_api_routes_are_accessible(): void
    {
        $response = $this->getJson('/api/company-types');

        $response->assertStatus(200);
    }

    /** @test */
    public function database_connection_works(): void
    {
        $user = User::factory()->create();

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
        ]);
    }

    /** @test */
    public function models_can_be_created(): void
    {
        $user = User::factory()->create();
        $company = CustomerCompany::factory()->create();
        $role = AppRole::factory()->create();

        $this->assertInstanceOf(User::class, $user);
        $this->assertInstanceOf(CustomerCompany::class, $company);
        $this->assertInstanceOf(AppRole::class, $role);
    }

    /** @test */
    public function relationships_work_correctly(): void
    {
        $user = User::factory()->create();
        $company = CustomerCompany::factory()->create(['user_id' => $user->id]);
        $role = UserRole::factory()->create([
            'user_id' => $user->id,
            'role_id' => $this->developerRole->id,
        ]);

        $this->assertCount(1, $user->companies);
        $this->assertEquals($company->id, $user->companies->first()->id);
        $this->assertEquals($this->developerRole->id, $user->appRole->role->id);
    }

    /** @test */
    public function session_works_correctly(): void
    {
        session(['test_key' => 'test_value']);

        $this->assertEquals('test_value', session('test_key'));
    }

    /** @test */
    public function authentication_works_correctly(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('password123'),
        ]);

        $this->actingAs($user);

        $this->assertTrue(auth()->check());
        $this->assertEquals($user->id, auth()->id());
    }

    /** @test */
    public function middleware_stack_is_working(): void
    {
        $response = $this->get('/man/customer-company');

        $response->assertRedirect('/auth/login');
    }

    /** @test */
    public function csrf_protection_is_enabled(): void
    {
        $response = $this->post('/auth/login');

        $response->assertStatus(419); // CSRF token mismatch
    }

    /** @test */
    public function error_pages_are_accessible(): void
    {
        $response = $this->get('/nonexistent-page');

        $response->assertStatus(404);
    }

    /** @test */
    public function application_environment_is_setup(): void
    {
        $this->assertNotNull(config('app.name'));
        $this->assertNotNull(config('app.env'));
        $this->assertNotNull(config('database.default'));
    }
}
