<?php

namespace Tests\Feature\Man;

use App\Models\AppRole;
use App\Models\BusinessType;
use App\Models\CompanyAddress;
use App\Models\CustomerCompany;
use App\Models\CustomerRole;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CustomerCompanyControllerTest extends TestCase
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
    public function customer_company_index_page_can_be_accessed()
    {
        $user = User::factory()->create();
        UserRole::factory()->create([
            'userId' => $user->id,
            'roleId' => $this->developerRole->id,
        ]);

        $this->actingAs($user);

        $response = $this->get('/man/customer-company');

        $response->assertStatus(200)
            ->assertViewIs('man.customer-company');
    }

    /** @test */
    public function customer_company_data_table_returns_data()
    {
        $user = User::factory()->create();
        UserRole::factory()->create([
            'userId' => $user->id,
            'roleId' => $this->developerRole->id,
        ]);

        $company = CustomerCompany::factory()->create([
            'userId' => $user->id,
            'businessId' => $this->businessType->id,
        ]);

        CompanyAddress::factory()->create([
            'companyId' => $company->id,
        ]);

        $this->actingAs($user);

        $response = $this->get('/man/customer-company/data-table');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'draw',
                'recordsFiltered',
                'recordsTotal',
                'aaData',
            ]);
    }

    /** @test */
    public function customer_company_can_be_stored()
    {
        $user = User::factory()->create();
        UserRole::factory()->create([
            'userId' => $user->id,
            'roleId' => $this->developerRole->id,
        ]);

        $this->actingAs($user);

        $companyData = [
            'name' => 'Test Company',
            'phone_number' => '02112345678',
            'businessId' => $this->businessType->id,
            'userId' => $user->id,
            'address' => 'Test Address',
            'city' => 'Test City',
            'province' => 'Test Province',
            'zipCode' => '12345',
        ];

        $response = $this->post('/man/customer-company', $companyData);

        $response->assertStatus(200);

        $this->assertDatabaseHas('customer_companies', [
            'name' => 'Test Company',
            'phone_number' => '02112345678',
        ]);

        $this->assertDatabaseHas('company_addresses', [
            'address' => 'Test Address',
            'city' => 'Test City',
        ]);
    }

    /** @test */
    public function customer_company_store_fails_with_invalid_data()
    {
        $user = User::factory()->create();
        UserRole::factory()->create([
            'userId' => $user->id,
            'roleId' => $this->developerRole->id,
        ]);

        $this->actingAs($user);

        $response = $this->post('/man/customer-company', [
            'name' => '',
            'phone_number' => 'invalid-phone',
        ]);

        $response->assertSessionHasErrors(['name', 'phone_number']);
    }

    /** @test */
    public function customer_company_can_be_updated()
    {
        $user = User::factory()->create();
        UserRole::factory()->create([
            'userId' => $user->id,
            'roleId' => $this->developerRole->id,
        ]);

        $company = CustomerCompany::factory()->create([
            'userId' => $user->id,
            'businessId' => $this->businessType->id,
        ]);

        $this->actingAs($user);

        $updateData = [
            'name' => 'Updated Company',
            'phone_number' => '02187654321',
            'businessId' => $this->businessType->id,
            'address' => 'Updated Address',
            'city' => 'Updated City',
            'province' => 'Updated Province',
            'zipCode' => '54321',
        ];

        $response = $this->put("/man/customer-company/{$company->id}", $updateData);

        $response->assertStatus(200);

        $this->assertDatabaseHas('customer_companies', [
            'id' => $company->id,
            'name' => 'Updated Company',
            'phone_number' => '02187654321',
        ]);
    }

    /** @test */
    public function customer_company_can_be_deleted()
    {
        $user = User::factory()->create();
        UserRole::factory()->create([
            'userId' => $user->id,
            'roleId' => $this->developerRole->id,
        ]);

        $company = CustomerCompany::factory()->create([
            'userId' => $user->id,
        ]);

        $this->actingAs($user);

        $response = $this->delete("/man/customer-company/{$company->id}");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('customer_companies', [
            'id' => $company->id,
        ]);
    }

    /** @test */
    public function customer_company_show_returns_company_data()
    {
        $user = User::factory()->create();
        UserRole::factory()->create([
            'userId' => $user->id,
            'roleId' => $this->developerRole->id,
        ]);

        $company = CustomerCompany::factory()->create([
            'userId' => $user->id,
            'businessId' => $this->businessType->id,
        ]);

        $this->actingAs($user);

        $response = $this->get("/man/customer-company/{$company->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'name',
                'phone_number',
                'businessId',
                'userId',
            ]);
    }

    /** @test */
    public function customer_company_profile_can_be_accessed()
    {
        $user = User::factory()->create();
        UserRole::factory()->create([
            'userId' => $user->id,
            'roleId' => $this->developerRole->id,
        ]);

        $this->actingAs($user);

        $response = $this->get('/man/customer-company/profile');

        $response->assertStatus(200);
    }

    /** @test */
    public function customer_company_login_can_be_performed()
    {
        $user = User::factory()->create();
        UserRole::factory()->create([
            'userId' => $user->id,
            'roleId' => $this->developerRole->id,
        ]);

        $company = CustomerCompany::factory()->create([
            'userId' => $user->id,
        ]);

        $this->actingAs($user);

        $response = $this->post('/man/customer-company/login-company', [
            'company_id' => $company->id,
        ]);

        $response->assertStatus(200);
    }

    /** @test */
    public function manager_can_only_see_their_companies()
    {
        $manager = User::factory()->create();
        UserRole::factory()->create([
            'userId' => $manager->id,
            'roleId' => $this->managerRole->id,
        ]);

        $otherUser = User::factory()->create();
        UserRole::factory()->create([
            'userId' => $otherUser->id,
            'roleId' => $this->developerRole->id,
        ]);

        // Create companies for both users
        $managerCompany = CustomerCompany::factory()->create([
            'userId' => $manager->id,
        ]);

        $otherCompany = CustomerCompany::factory()->create([
            'userId' => $otherUser->id,
        ]);

        $this->actingAs($manager);

        $response = $this->get('/man/customer-company/data-table');

        $response->assertStatus(200);

        $data = $response->json('aaData');

        // Manager should only see their own company
        $this->assertCount(1, $data);
        $this->assertStringContainsString($managerCompany->name, $data[0]['name']);
    }

    /** @test */
    public function developer_can_see_all_companies()
    {
        $developer = User::factory()->create();
        UserRole::factory()->create([
            'userId' => $developer->id,
            'roleId' => $this->developerRole->id,
        ]);

        $otherUser = User::factory()->create();
        UserRole::factory()->create([
            'userId' => $otherUser->id,
            'roleId' => $this->managerRole->id,
        ]);

        // Create companies for both users
        $developerCompany = CustomerCompany::factory()->create([
            'userId' => $developer->id,
        ]);

        $otherCompany = CustomerCompany::factory()->create([
            'userId' => $otherUser->id,
        ]);

        $this->actingAs($developer);

        $response = $this->get('/man/customer-company/data-table');

        $response->assertStatus(200);

        $data = $response->json('aaData');

        // Developer should see all companies
        $this->assertCount(2, $data);
    }

    /** @test */
    public function data_table_search_works_correctly()
    {
        $user = User::factory()->create();
        UserRole::factory()->create([
            'userId' => $user->id,
            'roleId' => $this->developerRole->id,
        ]);

        $company1 = CustomerCompany::factory()->create([
            'userId' => $user->id,
            'name' => 'Test Company One',
        ]);

        $company2 = CustomerCompany::factory()->create([
            'userId' => $user->id,
            'name' => 'Another Company',
        ]);

        $this->actingAs($user);

        $response = $this->get('/man/customer-company/data-table?search[value]=Test');

        $response->assertStatus(200);

        $data = $response->json('aaData');

        // Should only return companies with "Test" in the name
        $this->assertCount(1, $data);
        $this->assertStringContainsString('Test Company One', $data[0]['name']);
    }

    /** @test */
    public function data_table_pagination_works()
    {
        $user = User::factory()->create();
        UserRole::factory()->create([
            'userId' => $user->id,
            'roleId' => $this->developerRole->id,
        ]);

        // Create multiple companies
        for ($i = 1; $i <= 15; $i++) {
            CustomerCompany::factory()->create([
                'userId' => $user->id,
                'name' => "Company {$i}",
            ]);
        }

        $this->actingAs($user);

        $response = $this->get('/man/customer-company/data-table?length=10&start=0');

        $response->assertStatus(200);

        $data = $response->json('aaData');

        // Should return only 10 records
        $this->assertCount(10, $data);
    }
}
