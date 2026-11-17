<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Lunar\Models\Channel;
use Lunar\Models\Currency;
use Lunar\Models\CustomerGroup;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use LazilyRefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        Channel::factory()->create(['default' => true]);
        Currency::factory()->create(['default' => true]);
        CustomerGroup::factory()->create(['default' => true]);

        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
