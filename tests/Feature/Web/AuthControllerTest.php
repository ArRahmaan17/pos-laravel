<?php

namespace Tests\Feature\Web;

use App\Models\AppRole;
use App\Models\BusinessType;
use App\Models\CustomerCompany;
use App\Models\CustomerRole;
use App\Models\User;
use App\Models\UserCustomerRole;
use App\Models\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthControllerTest extends TestCase
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
    public function login_page_can_be_accessed()
    {
        $response = $this->get('/auth/login');

        $response->assertStatus(200)
            ->assertViewIs('auth.login');
    }

    /** @test */
    public function registration_page_can_be_accessed()
    {
        $response = $this->get('/auth/registration');

        $response->assertStatus(200)
            ->assertViewIs('auth.registration');
    }

    /** @test */
    public function user_can_login_with_valid_credentials()
    {
        $user = User::factory()->create([
            'username' => 'testuser',
            'password' => Hash::make('password123'),
        ]);

        UserRole::factory()->create([
            'userId' => $user->id,
            'roleId' => $this->developerRole->id,
        ]);

        $response = $this->post('/auth/login', [
            'username' => 'testuser',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/');
    }

    /** @test */
    public function login_fails_with_invalid_credentials()
    {
        $response = $this->post('/auth/login', [
            'username' => 'invaliduser',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors(['username']);
    }

    /** @test */
    public function user_can_register_with_valid_data()
    {
        $userData = [
            'name' => 'Test User',
            'username' => 'testuser123',
            'email' => 'test@example.com',
            'phone_number' => '081234567890',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'company_name' => 'Test Company',
            'company_phone' => '02112345678',
            'business_type_id' => $this->businessType->id,
            'address' => 'Test Address',
            'city' => 'Test City',
            'province' => 'Test Province',
            'zip_code' => '12345',
        ];

        $response = $this->post('/auth/registration', $userData);

        $response->assertRedirect('/');

        $this->assertDatabaseHas('users', [
            'username' => 'testuser123',
            'email' => 'test@example.com',
        ]);

        $this->assertDatabaseHas('customer_companies', [
            'name' => 'Test Company',
        ]);
    }

    /** @test */
    public function registration_fails_with_invalid_data()
    {
        $response = $this->post('/auth/registration', [
            'name' => '',
            'username' => 'short',
            'email' => 'invalid-email',
            'password' => 'short',
            'password_confirmation' => 'different',
        ]);

        $response->assertSessionHasErrors([
            'name',
            'username',
            'email',
            'password',
            'password_confirmation',
        ]);
    }

    /** @test */
    public function authenticated_user_can_access_home_page()
    {
        $user = User::factory()->create();
        UserRole::factory()->create([
            'userId' => $user->id,
            'roleId' => $this->developerRole->id,
        ]);

        $this->actingAs($user);

        $response = $this->get('/');

        $response->assertStatus(200)
            ->assertViewIs('home');
    }

    /** @test */
    public function unauthenticated_user_cannot_access_home_page()
    {
        $response = $this->get('/');

        $response->assertRedirect('/auth/login');
    }

    /** @test */
    public function authenticated_user_can_logout()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/auth/logout');

        $response->assertRedirect('/auth/login');
    }

    /** @test */
    public function user_can_access_select_company_page()
    {
        $user = User::factory()->create();
        UserRole::factory()->create([
            'userId' => $user->id,
            'roleId' => $this->developerRole->id,
        ]);

        $this->actingAs($user);

        $response = $this->get('/select-company');

        $response->assertStatus(200)
            ->assertViewIs('select-customer-company');
    }

    /** @test */
    public function user_can_select_company()
    {
        $user = User::factory()->create();
        $company = CustomerCompany::factory()->create();

        UserCustomerRole::factory()->create([
            'userId' => $user->id,
            'roleId' => $this->customerRole->id,
            'companyId' => $company->id,
        ]);

        $this->actingAs($user);

        $response = $this->post('/select-company', [
            'company_id' => $company->id,
        ]);

        $response->assertRedirect('/');
    }

    /** @test */
    public function user_can_access_request_access_pin_page()
    {
        $response = $this->get('/privacy/request-access-pin');

        $response->assertStatus(200);
    }

    /** @test */
    public function user_can_validate_access_pin()
    {
        $user = User::factory()->create([
            'access_pin' => Hash::make('123456'),
        ]);
        $this->actingAs($user);
        $response = $this->post('/privacy/validate-access-pin', [
            'access_pin' => '123456',
        ]);

        $response->assertStatus(200);
    }

    /** @test */
    public function user_can_activate_access_pin()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post('/privacy/access-pin', [
            'access_pin' => '123456',
            'access_pin_confirmation' => '123456',
        ]);

        $response->assertStatus(200);
    }

    /** @test */
    public function authenticated_user_can_access_lockscreen()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post('/auth/lockscreen');

        $response->assertStatus(200);
    }

    /** @test */
    public function user_can_unlock_screen()
    {
        $user = User::factory()->create([
            'access_pin' => Hash::make('123456'),
        ]);
        $this->actingAs($user);

        $response = $this->post('/auth/unlock-screen', [
            'access_pin' => '123456',
        ]);

        $response->assertStatus(200);
    }

    /** @test */
    public function unlock_screen_fails_with_wrong_pin()
    {
        $user = User::factory()->create([
            'access_pin' => Hash::make('123456'),
        ]);
        $this->actingAs($user);

        $response = $this->post('/auth/unlock-screen', [
            'access_pin' => '654321',
        ]);

        $response->assertSessionHasErrors(['access_pin']);
    }

    /** @test */
    public function authenticated_user_can_request_change_password()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/auth/request-change-password');

        $response->assertStatus(200);
    }

    /** @test */
    public function authenticated_user_can_access_change_company()
    {
        $user = User::factory()->create();
        UserRole::factory()->create([
            'userId' => $user->id,
            'roleId' => $this->developerRole->id,
        ]);

        $this->actingAs($user);

        $response = $this->get('/auth/change-company');

        $response->assertStatus(200);
    }
}
