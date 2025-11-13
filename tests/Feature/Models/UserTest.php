<?php

namespace Tests\Feature\Models;

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

class UserTest extends TestCase
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
    public function user_can_be_created()
    {
        $userData = [
            'name' => 'Test User',
            'username' => 'testuser',
            'email' => 'test@example.com',
            'phone_number' => '081234567890',
            'password' => Hash::make('password123'),
        ];

        $user = User::create($userData);

        $this->assertDatabaseHas('users', [
            'name' => 'Test User',
            'username' => 'testuser',
            'email' => 'test@example.com',
        ]);

        $this->assertInstanceOf(User::class, $user);
    }

    /** @test */
    public function user_has_required_fields()
    {
        $user = User::factory()->create();

        $this->assertNotNull($user->name);
        $this->assertNotNull($user->username);
        $this->assertNotNull($user->email);
        $this->assertNotNull($user->phone_number);
    }

    /** @test */
    public function user_can_have_app_role()
    {
        $user = User::factory()->create();
        $role = UserRole::factory()->create([
            'user_id' => $user->id,
            'role_id' => $this->developerRole->id,
        ]);

        $this->assertInstanceOf(UserRole::class, $user->appRole);
        $this->assertEquals($this->developerRole->id, $user->appRole->role->id);
    }

    /** @test */
    public function user_can_have_customer_role()
    {
        $user = User::factory()->create();
        $company = CustomerCompany::factory()->create();

        $role = UserCustomerRole::factory()->create([
            'user_id' => $user->id,
            'role_id' => $this->customerRole->id,
            'company_id' => $company->id,
        ]);

        $this->assertInstanceOf(UserCustomerRole::class, $user->customerRole);
        $this->assertEquals($this->customerRole->id, $user->customerRole->role->id);
    }

    /** @test */
    public function user_can_have_multiple_customer_roles()
    {
        $user = User::factory()->create();
        $company1 = CustomerCompany::factory()->create();
        $company2 = CustomerCompany::factory()->create();

        $role1 = UserCustomerRole::factory()->create([
            'user_id' => $user->id,
            'role_id' => $this->customerRole->id,
            'company_id' => $company1->id,
        ]);

        $role2 = UserCustomerRole::factory()->create([
            'user_id' => $user->id,
            'role_id' => $this->customerRole->id,
            'company_id' => $company2->id,
        ]);

        $this->assertCount(2, $user->customerRoles);
    }

    /** @test */
    public function user_can_have_companies()
    {
        $user = User::factory()->create();
        $company1 = CustomerCompany::factory()->create(['user_id' => $user->id]);
        $company2 = CustomerCompany::factory()->create(['user_id' => $user->id]);

        $this->assertCount(2, $user->companies);
        $this->assertInstanceOf(CustomerCompany::class, $user->companies->first());
    }

    /** @test */
    public function user_scope_manager_returns_manager_users()
    {
        $manager = User::factory()->create();
        $developer = User::factory()->create();
        $customer = User::factory()->create();

        UserRole::factory()->create([
            'user_id' => $manager->id,
            'role_id' => $this->managerRole->id,
        ]);

        UserRole::factory()->create([
            'user_id' => $developer->id,
            'role_id' => $this->developerRole->id,
        ]);

        $managerUsers = User::user_manager()->get();

        $this->assertTrue($managerUsers->contains($manager));
        $this->assertFalse($managerUsers->contains($developer));
        $this->assertFalse($managerUsers->contains($customer));
    }

    /** @test */
    public function user_can_have_access_pin()
    {
        $user = User::factory()->create([
            'access_pin' => Hash::make('123456'),
        ]);

        $this->assertNotNull($user->access_pin);
        $this->assertTrue(Hash::check('123456', $user->access_pin));
    }

    /** @test */
    public function user_can_have_affiliate_code()
    {
        $user = User::factory()->create([
            'affiliate_code' => 'ABC123',
        ]);

        $this->assertEquals('ABC123', $user->affiliate_code);
    }

    /** @test */
    public function user_can_have_registration_link()
    {
        $user = User::factory()->create([
            'registration_link' => 'https://example.com/register/ABC123',
        ]);

        $this->assertEquals('https://example.com/register/ABC123', $user->registration_link);
    }

    /** @test */
    public function user_can_be_soft_deleted()
    {
        $user = User::factory()->create();

        $user->delete();

        $this->assertSoftDeleted('users', ['id' => $user->id]);
    }

    /** @test */
    public function user_can_be_restored()
    {
        $user = User::factory()->create();

        $user->delete();
        $user->restore();

        $this->assertDatabaseHas('users', ['id' => $user->id]);
    }

    /** @test */
    public function user_can_be_permanently_deleted()
    {
        $user = User::factory()->create();

        $user->forceDelete();

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    /** @test */
    public function user_has_hidden_fields()
    {
        $user = User::factory()->create();

        $userArray = $user->toArray();

        $this->assertArrayNotHasKey('password', $userArray);
        $this->assertArrayNotHasKey('access_pin', $userArray);
    }

    /** @test */
    public function user_can_have_remember_token()
    {
        $user = User::factory()->create([
            'remember_token' => 'test-token',
        ]);

        $this->assertEquals('test-token', $user->remember_token);
    }

    /** @test */
    public function user_can_have_email_verified_at()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $this->assertNotNull($user->email_verified_at);
    }

    /** @test */
    public function user_can_have_phone_verified_at()
    {
        $user = User::factory()->create([
            'phone_verified_at' => now(),
        ]);

        $this->assertNotNull($user->phone_verified_at);
    }

    /** @test */
    public function user_can_have_profile_picture()
    {
        $user = User::factory()->create([
            'profile_picture' => 'profile.jpg',
        ]);

        $this->assertEquals('profile.jpg', $user->profile_picture);
    }

    /** @test */
    public function user_can_have_status()
    {
        $user = User::factory()->create([
            'status' => 'active',
        ]);

        $this->assertEquals('active', $user->status);
    }

    /** @test */
    public function user_can_have_last_login_at()
    {
        $user = User::factory()->create([
            'last_login_at' => now(),
        ]);

        $this->assertNotNull($user->last_login_at);
    }

    /** @test */
    public function user_can_have_last_login_ip()
    {
        $user = User::factory()->create([
            'last_login_ip' => '192.168.1.1',
        ]);

        $this->assertEquals('192.168.1.1', $user->last_login_ip);
    }

    /** @test */
    public function user_can_have_last_login_user_agent()
    {
        $user = User::factory()->create([
            'last_login_user_agent' => 'Mozilla/5.0',
        ]);

        $this->assertEquals('Mozilla/5.0', $user->last_login_user_agent);
    }

    /** @test */
    public function user_can_have_created_by()
    {
        $creator = User::factory()->create();
        $user = User::factory()->create([
            'created_by' => $creator->id,
        ]);

        $this->assertEquals($creator->id, $user->created_by);
    }

    /** @test */
    public function user_can_have_updated_by()
    {
        $updater = User::factory()->create();
        $user = User::factory()->create([
            'updated_by' => $updater->id,
        ]);

        $this->assertEquals($updater->id, $user->updated_by);
    }
}
