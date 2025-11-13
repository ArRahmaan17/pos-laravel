<?php

namespace Tests\Unit;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function test_example(): void
    {
        $this->assertTrue(true);
    }

    /**
     * Test that basic PHP functions work.
     */
    public function test_basic_php_functions(): void
    {
        $this->assertEquals(4, 2 + 2);
        $this->assertIsString('hello world');
        $this->assertIsArray([]);
    }

    /**
     * Test that Laravel helpers are available.
     */
    public function test_laravel_helpers(): void
    {
        $this->assertIsString(config('app.name'));
        $this->assertIsString(config('app.env'));
    }
}
