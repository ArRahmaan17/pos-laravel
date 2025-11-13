<?php

namespace Tests\Feature\Api;

use App\Models\AppRole;
use App\Models\BusinessType;
use App\Models\CustomerCompany;
use App\Models\CustomerRole;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
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
    public function user_can_login_with_valid_credentials()
    {
        $user = User::factory()->create([
            'username' => 'testuser',
            'password' => Hash::make('password123'),
            'email' => 'test@example.com',
        ]);

        UserRole::factory()->create([
            'user_id' => $user->id,
            'role_id' => $this->developerRole->id,
        ]);

        $response = $this->postJson('/api/login', [
            'username' => 'testuser',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'token',
                'user' => [
                    'user',
                    'role',
                    'user_id',
                ],
                'token_type',
            ])
            ->assertJson([
                'message' => 'Login successful',
                'token_type' => 'Bearer',
            ]);
    }

    /** @test */
    public function user_can_login_with_email()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        UserRole::factory()->create([
            'user_id' => $user->id,
            'role_id' => $this->developerRole->id,
        ]);

        $response = $this->postJson('/api/login', [
            'username' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200);
    }

    /** @test */
    public function user_can_login_with_phone_number()
    {
        $user = User::factory()->create([
            'phone_number' => '081234567890',
            'password' => Hash::make('password123'),
        ]);

        UserRole::factory()->create([
            'user_id' => $user->id,
            'role_id' => $this->developerRole->id,
        ]);

        $response = $this->postJson('/api/login', [
            'username' => '081234567890',
            'password' => 'password123',
        ]);

        $response->assertStatus(200);
    }

    /** @test */
    public function login_fails_with_invalid_credentials()
    {
        $response = $this->postJson('/api/login', [
            'username' => 'invaliduser',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['username']);
    }

    /** @test */
    public function login_fails_with_invalid_validation()
    {
        $response = $this->postJson('/api/login', [
            'username' => 'short',
            'password' => 'short',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['username', 'password']);
    }

    /** @test */
    public function login_fails_for_user_without_role()
    {
        $user = User::factory()->create([
            'username' => 'testuser',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'username' => 'testuser',
            'password' => 'password123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['username']);
    }

    /** @test */
    public function authenticated_user_can_logout()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/logout');

        $response->assertStatus(200)
            ->assertJson(['message' => 'Successfully logged out']);
    }

    /** @test */
    public function authenticated_user_can_logout_from_all_devices()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/logout-all');

        $response->assertStatus(200)
            ->assertJson(['message' => 'Successfully logged out from all devices']);
    }

    /** @test */
    public function authenticated_user_can_get_profile()
    {
        $user = User::factory()->create();
        UserRole::factory()->create([
            'user_id' => $user->id,
            'role_id' => $this->developerRole->id,
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/me');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'user',
                'role',
                'user_id',
            ]);
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

        $response = $this->postJson('/api/register', $userData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'token',
                'user' => [
                    'user',
                    'role',
                    'user_id',
                    'company',
                ],
                'token_type',
            ]);

        $this->assertDatabaseHas('users', [
            'username' => 'testuser123',
            'email' => 'test@example.com',
        ]);

        $this->assertDatabaseHas('companies', [
            'name' => 'Test Company',
        ]);
    }

    /** @test */
    public function register_fails_with_invalid_data()
    {
        $response = $this->postJson('/api/register', [
            'name' => '',
            'username' => 'short',
            'email' => 'invalid-email',
            'password' => 'short',
            'password_confirmation' => 'different',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'name',
                'username',
                'email',
                'password',
                'password_confirmation',
            ]);
    }

    /** @test */
    public function register_fails_with_duplicate_username()
    {
        User::factory()->create(['username' => 'existinguser']);

        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'username' => 'existinguser',
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
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['username']);
    }

    /** @test */
    public function user_can_check_username_availability()
    {
        User::factory()->create(['username' => 'existinguser']);

        // Check existing username
        $response = $this->getJson('/api/check-available-user?username=existinguser');
        $response->assertStatus(200)
            ->assertJson(['available' => false]);

        // Check new username
        $response = $this->getJson('/api/check-available-user?username=newuser');
        $response->assertStatus(200)
            ->assertJson(['available' => true]);
    }

    /** @test */
    public function user_can_check_company_availability()
    {
        CustomerCompany::factory()->create(['name' => 'Existing Company']);

        // Check existing company
        $response = $this->getJson('/api/check-company-availability?name=Existing Company');
        $response->assertStatus(200)
            ->assertJson(['available' => false]);

        // Check new company
        $response = $this->getJson('/api/check-company-availability?name=New Company');
        $response->assertStatus(200)
            ->assertJson(['available' => true]);
    }

    /** @test */
    public function user_can_get_company_types()
    {
        $response = $this->getJson('/api/company-types');

        $response->assertStatus(200)
            ->assertJsonStructure(['business_types']);
    }

    /** @test */
    public function authenticated_user_can_change_password()
    {
        $user = User::factory()->create([
            'password' => Hash::make('oldpassword'),
        ]);
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/change-password', [
            'current_password' => 'oldpassword',
            'new_password' => 'newpassword123',
            'new_password_confirmation' => 'newpassword123',
        ]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Password changed successfully']);
    }

    /** @test */
    public function change_password_fails_with_wrong_current_password()
    {
        $user = User::factory()->create([
            'password' => Hash::make('oldpassword'),
        ]);
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/change-password', [
            'current_password' => 'wrongpassword',
            'new_password' => 'newpassword123',
            'new_password_confirmation' => 'newpassword123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['current_password']);
    }

    /** @test */
    public function user_can_activate_access_pin()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/activate-access-pin', [
            'access_pin' => '123456',
            'access_pin_confirmation' => '123456',
        ]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Access PIN activated successfully']);
    }

    /** @test */
    public function user_can_unlock_screen()
    {
        $user = User::factory()->create([
            'access_pin' => Hash::make('123456'),
        ]);
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/unlock-screen', [
            'access_pin' => '123456',
        ]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Screen unlocked successfully']);
    }

    /** @test */
    public function unlock_screen_fails_with_wrong_pin()
    {
        $user = User::factory()->create([
            'access_pin' => Hash::make('123456'),
        ]);
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/unlock-screen', [
            'access_pin' => '654321',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['access_pin']);
    }
}
