<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class PunkApiTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        // Seed database with default user
        $this->artisan('db:seed');
    }

    /**
     * @test
     */
    public function guest_user_cannot_use_punk_api(): void
    {
        $response = $this->getJson('/api/beers');

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function authenticated_user_can_use_punk_api(): void
    {
        $user = User::firstOrFail();
        $response = $this->actingAs($user)->getJson('/api/beers');

        $response->assertStatus(Response::HTTP_OK);
    }
}
