<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApplicationTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_login_view_returns_a_successful_response(): void
    {
        $response = $this->get('/auth/login');

        $response->assertStatus(200);
    }
}
