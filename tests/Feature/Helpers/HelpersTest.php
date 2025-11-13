<?php

namespace Tests\Feature\Helpers;

use App\Models\AppRole;
use App\Models\BusinessType;
use App\Models\CustomerCompany;
use App\Models\CustomerRole;
use App\Models\User;
use App\Models\UserCustomerRole;
use App\Models\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class HelpersTest extends TestCase
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
    public function get_role_returns_user_role()
    {
        $user = User::factory()->create();
        UserRole::factory()->create([
            'user_id' => $user->id,
            'role_id' => $this->developerRole->id,
        ]);

        $this->actingAs($user);

        $role = getRole();

        $this->assertEquals('Developer', $role);
    }

    /** @test */
    public function get_role_returns_customer_role()
    {
        $user = User::factory()->create();
        $company = CustomerCompany::factory()->create();

        UserCustomerRole::factory()->create([
            'user_id' => $user->id,
            'role_id' => $this->customerRole->id,
            'company_id' => $company->id,
        ]);

        $this->actingAs($user);

        $role = getRole();

        $this->assertEquals('Customer', $role);
    }

    /** @test */
    public function get_role_returns_null_for_user_without_role()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $role = getRole();

        $this->assertNull($role);
    }

    /** @test */
    public function format_indonesian_phone_number_formats_correctly()
    {
        $phoneNumber = '081234567890';
        $formatted = formatIndonesianPhoneNumber($phoneNumber);

        $this->assertEquals('+62 812-3456-7890', $formatted);
    }

    /** @test */
    public function format_indonesian_phone_number_handles_different_formats()
    {
        $phoneNumbers = [
            '081234567890' => '+62 812-3456-7890',
            '6281234567890' => '+62 812-3456-7890',
            '81234567890' => '+62 812-3456-7890',
            '02112345678' => '+62 21-1234-5678',
            '2112345678' => '+62 21-1234-5678',
        ];

        foreach ($phoneNumbers as $input => $expected) {
            $formatted = formatIndonesianPhoneNumber($input);
            $this->assertEquals($expected, $formatted);
        }
    }

    /** @test */
    public function format_indonesian_phone_number_handles_invalid_input()
    {
        $invalidNumbers = ['', 'invalid', '123', '123456789'];

        foreach ($invalidNumbers as $number) {
            $formatted = formatIndonesianPhoneNumber($number);
            $this->assertEquals($number, $formatted);
        }
    }

    /** @test */
    public function format_currency_formats_correctly()
    {
        $amount = 1000000;
        $formatted = formatCurrency($amount);

        $this->assertEquals('Rp 1.000.000', $formatted);
    }

    /** @test */
    public function format_currency_handles_decimal_values()
    {
        $amount = 1000000.50;
        $formatted = formatCurrency($amount);

        $this->assertEquals('Rp 1.000.000,50', $formatted);
    }

    /** @test */
    public function format_currency_handles_zero()
    {
        $amount = 0;
        $formatted = formatCurrency($amount);

        $this->assertEquals('Rp 0', $formatted);
    }

    /** @test */
    public function format_currency_handles_negative_values()
    {
        $amount = -1000000;
        $formatted = formatCurrency($amount);

        $this->assertEquals('-Rp 1.000.000', $formatted);
    }

    /** @test */
    public function format_date_formats_correctly()
    {
        $date = '2024-01-15';
        $formatted = formatDate($date);

        $this->assertEquals('15 Januari 2024', $formatted);
    }

    /** @test */
    public function format_date_handles_datetime()
    {
        $datetime = '2024-01-15 14:30:00';
        $formatted = formatDate($datetime);

        $this->assertEquals('15 Januari 2024', $formatted);
    }

    /** @test */
    public function format_datetime_formats_correctly()
    {
        $datetime = '2024-01-15 14:30:00';
        $formatted = formatDateTime($datetime);

        $this->assertEquals('15 Januari 2024 14:30', $formatted);
    }

    /** @test */
    public function format_datetime_handles_date_only()
    {
        $date = '2024-01-15';
        $formatted = formatDateTime($date);

        $this->assertEquals('15 Januari 2024 00:00', $formatted);
    }

    /** @test */
    public function generate_random_string_generates_correct_length()
    {
        $length = 10;
        $randomString = generateRandomString($length);

        $this->assertEquals($length, strlen($randomString));
    }

    /** @test */
    public function generate_random_string_generates_unique_strings()
    {
        $strings = [];
        for ($i = 0; $i < 100; $i++) {
            $strings[] = generateRandomString(10);
        }

        $uniqueStrings = array_unique($strings);
        $this->assertEquals(count($strings), count($uniqueStrings));
    }

    /** @test */
    public function generate_random_string_uses_correct_characters()
    {
        $randomString = generateRandomString(20);

        // Should only contain alphanumeric characters
        $this->assertMatchesRegularExpression('/^[a-zA-Z0-9]+$/', $randomString);
    }

    /** @test */
    public function validate_email_validates_correct_emails()
    {
        $validEmails = [
            'test@example.com',
            'user.name@domain.co.uk',
            'user+tag@example.org',
            '123@example.com',
        ];

        foreach ($validEmails as $email) {
            $this->assertTrue(validateEmail($email));
        }
    }

    /** @test */
    public function validate_email_rejects_invalid_emails()
    {
        $invalidEmails = [
            'invalid-email',
            '@example.com',
            'user@',
            'user@.com',
            'user..name@example.com',
        ];

        foreach ($invalidEmails as $email) {
            $this->assertFalse(validateEmail($email));
        }
    }

    /** @test */
    public function validate_phone_number_validates_correct_numbers()
    {
        $validNumbers = [
            '081234567890',
            '6281234567890',
            '02112345678',
            '622112345678',
        ];

        foreach ($validNumbers as $number) {
            $this->assertTrue(validatePhoneNumber($number));
        }
    }

    /** @test */
    public function validate_phone_number_rejects_invalid_numbers()
    {
        $invalidNumbers = [
            '123',
            '123456789',
            'invalid',
            '081234567890123456789',
        ];

        foreach ($invalidNumbers as $number) {
            $this->assertFalse(validatePhoneNumber($number));
        }
    }

    /** @test */
    public function slugify_creates_valid_slugs()
    {
        $strings = [
            'Hello World' => 'hello-world',
            'Test String 123' => 'test-string-123',
            'Special@#$%Characters' => 'special-characters',
            'Multiple   Spaces' => 'multiple-spaces',
            'UPPERCASE' => 'uppercase',
        ];

        foreach ($strings as $input => $expected) {
            $slug = slugify($input);
            $this->assertEquals($expected, $slug);
        }
    }

    /** @test */
    public function slugify_handles_empty_string()
    {
        $slug = slugify('');
        $this->assertEquals('', $slug);
    }

    /** @test */
    public function truncate_text_truncates_long_text()
    {
        $longText = 'This is a very long text that should be truncated to a shorter length for display purposes.';
        $truncated = truncateText($longText, 30);

        $this->assertLessThanOrEqual(33, strlen($truncated)); // 30 + '...'
        $this->assertStringEndsWith('...', $truncated);
    }

    /** @test */
    public function truncate_text_does_not_truncate_short_text()
    {
        $shortText = 'Short text';
        $truncated = truncateText($shortText, 30);

        $this->assertEquals($shortText, $truncated);
    }

    /** @test */
    public function truncate_text_handles_empty_string()
    {
        $truncated = truncateText('', 30);
        $this->assertEquals('', $truncated);
    }

    /** @test */
    public function mask_email_masks_correctly()
    {
        $email = 'user@example.com';
        $masked = maskEmail($email);

        $this->assertEquals('u***@example.com', $masked);
    }

    /** @test */
    public function mask_email_handles_short_username()
    {
        $email = 'a@example.com';
        $masked = maskEmail($email);

        $this->assertEquals('a***@example.com', $masked);
    }

    /** @test */
    public function mask_phone_number_masks_correctly()
    {
        $phone = '081234567890';
        $masked = maskPhoneNumber($phone);

        $this->assertEquals('0812****7890', $masked);
    }

    /** @test */
    public function mask_phone_number_handles_short_number()
    {
        $phone = '0812345678';
        $masked = maskPhoneNumber($phone);

        $this->assertEquals('0812****5678', $masked);
    }

    /** @test */
    public function is_ajax_request_detects_ajax_requests()
    {
        $request = request();
        $request->headers->set('X-Requested-With', 'XMLHttpRequest');

        $this->assertTrue(isAjaxRequest());
    }

    /** @test */
    public function is_ajax_request_detects_non_ajax_requests()
    {
        $request = request();
        $request->headers->remove('X-Requested-With');

        $this->assertFalse(isAjaxRequest());
    }

    /** @test */
    public function get_client_ip_returns_ip_address()
    {
        $ip = getClientIp();

        $this->assertNotNull($ip);
        $this->assertMatchesRegularExpression('/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/', $ip);
    }

    /** @test */
    public function get_user_agent_returns_user_agent()
    {
        $userAgent = getUserAgent();

        $this->assertNotNull($userAgent);
        $this->assertIsString($userAgent);
    }
}
