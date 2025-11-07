<?php

namespace Tests\Feature\Middleware;

use App\Models\AppRole;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthorizationTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        // Create necessary roles
        $this->developerRole = AppRole::factory()->create(['name' => 'Developer']);
        $this->managerRole = AppRole::factory()->create(['name' => 'Manager']);
    }

    /** @test */
    public function unauthenticated_user_is_redirected_to_login()
    {
        $response = $this->get('/');

        $response->assertRedirect('/auth/login');
    }

    /** @test */
    public function authenticated_user_can_access_protected_route()
    {
        $user = User::factory()->create();
        UserRole::factory()->create([
            'userId' => $user->id,
            'roleId' => $this->developerRole->id,
        ]);

        $this->actingAs($user);

        $response = $this->get('/');

        $response->assertStatus(200);
    }

    /** @test */
    public function user_without_role_cannot_access_protected_route()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->get('/');

        $response->assertRedirect('/auth/login');
    }

    /** @test */
    public function user_without_company_selection_is_redirected_to_select_company()
    {
        $user = User::factory()->create();
        UserRole::factory()->create([
            'userId' => $user->id,
            'roleId' => $this->developerRole->id,
        ]);

        $this->actingAs($user);

        // Clear any session data that might indicate company selection
        session()->forget('userLogged');

        $response = $this->get('/');

        $response->assertRedirect('/select-company');
    }

    /** @test */
    public function user_with_company_selection_can_access_protected_route()
    {
        $user = User::factory()->create();
        UserRole::factory()->create([
            'userId' => $user->id,
            'roleId' => $this->developerRole->id,
        ]);

        $this->actingAs($user);

        // Simulate company selection in session
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
    public function developer_can_access_all_routes()
    {
        $user = User::factory()->create();
        UserRole::factory()->create([
            'userId' => $user->id,
            'roleId' => $this->developerRole->id,
        ]);

        $this->actingAs($user);

        session([
            'userLogged' => [
                'user' => $user->toArray(),
                'company' => ['id' => 1, 'name' => 'Test Company'],
            ],
        ]);

        // Test access to developer routes
        $response = $this->get('/dev/app-role');
        $response->assertStatus(200);

        $response = $this->get('/dev/app-menu');
        $response->assertStatus(200);

        $response = $this->get('/dev/app-good-unit');
        $response->assertStatus(200);
    }

    /** @test */
    public function manager_cannot_access_developer_routes()
    {
        $user = User::factory()->create();
        UserRole::factory()->create([
            'userId' => $user->id,
            'roleId' => $this->managerRole->id,
        ]);

        $this->actingAs($user);

        session([
            'userLogged' => [
                'user' => $user->toArray(),
                'company' => ['id' => 1, 'name' => 'Test Company'],
            ],
        ]);

        // Test access to developer routes (should be denied)
        $response = $this->get('/dev/app-role');
        $response->assertStatus(403);

        $response = $this->get('/dev/app-menu');
        $response->assertStatus(403);
    }

    /** @test */
    public function manager_can_access_manager_routes()
    {
        $user = User::factory()->create();
        UserRole::factory()->create([
            'userId' => $user->id,
            'roleId' => $this->managerRole->id,
        ]);

        $this->actingAs($user);

        session([
            'userLogged' => [
                'user' => $user->toArray(),
                'company' => ['id' => 1, 'name' => 'Test Company'],
            ],
        ]);

        // Test access to manager routes
        $response = $this->get('/man/customer-company');
        $response->assertStatus(200);

        $response = $this->get('/man/customer-role');
        $response->assertStatus(200);
    }

    /** @test */
    public function user_without_access_pin_is_redirected_to_setup_pin()
    {
        $user = User::factory()->create([
            'access_pin' => null,
        ]);
        UserRole::factory()->create([
            'userId' => $user->id,
            'roleId' => $this->developerRole->id,
        ]);

        $this->actingAs($user);

        session([
            'userLogged' => [
                'user' => $user->toArray(),
                'company' => ['id' => 1, 'name' => 'Test Company'],
            ],
        ]);

        $response = $this->get('/');

        $response->assertRedirect('/privacy/request-access-pin');
    }

    /** @test */
    public function user_with_access_pin_can_access_protected_route()
    {
        $user = User::factory()->create([
            'access_pin' => Hash::make('123456'),
        ]);
        UserRole::factory()->create([
            'userId' => $user->id,
            'roleId' => $this->developerRole->id,
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
    public function middleware_handles_invalid_session_data()
    {
        $user = User::factory()->create();
        UserRole::factory()->create([
            'userId' => $user->id,
            'roleId' => $this->developerRole->id,
        ]);

        $this->actingAs($user);

        // Set invalid session data
        session([
            'userLogged' => [
                'user' => null,
                'company' => null,
            ],
        ]);

        $response = $this->get('/');

        $response->assertRedirect('/auth/login');
    }

    /** @test */
    public function middleware_handles_missing_session_data()
    {
        $user = User::factory()->create();
        UserRole::factory()->create([
            'userId' => $user->id,
            'roleId' => $this->developerRole->id,
        ]);

        $this->actingAs($user);

        // Clear session data
        session()->forget('userLogged');

        $response = $this->get('/');

        $response->assertRedirect('/select-company');
    }

    /** @test */
    public function middleware_preserves_original_request_url_in_redirect()
    {
        $response = $this->get('/man/customer-company');

        $response->assertRedirect('/auth/login');

        // The intended URL should be stored in session
        $this->assertEquals('/man/customer-company', session('url.intended'));
    }

    /** @test */
    public function middleware_handles_ajax_requests()
    {
        $response = $this->getJson('/man/customer-company/data-table');

        $response->assertStatus(401);
    }

    /** @test */
    public function middleware_handles_api_requests()
    {
        $response = $this->getJson('/api/me');

        $response->assertStatus(401);
    }

    /** @test */
    public function middleware_handles_post_requests()
    {
        $response = $this->post('/man/customer-company');

        $response->assertRedirect('/auth/login');
    }

    /** @test */
    public function middleware_handles_put_requests()
    {
        $response = $this->put('/man/customer-company/1');

        $response->assertRedirect('/auth/login');
    }

    /** @test */
    public function middleware_handles_delete_requests()
    {
        $response = $this->delete('/man/customer-company/1');

        $response->assertRedirect('/auth/login');
    }

    /** @test */
    public function middleware_handles_patch_requests()
    {
        $response = $this->patch('/man/customer-user/generate-affiliate-code');

        $response->assertRedirect('/auth/login');
    }
}
