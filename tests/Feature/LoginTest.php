<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class LoginTest extends TestCase
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
    public function user_with_invalid_credentials_cannot_login(): void
    {
        $user = User::firstOrFail();
        $response = $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'bad_password',
        ]);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function user_with_valid_credentials_can_login(): string
    {
        $user = User::firstOrFail();
        $response = $this->postJson('/api/auth/login', [
            'username' => $user->username,
            'password' => 'password',
        ]);

        $response->assertOk();
        $this->assertAuthenticatedAs($user);
        $response->assertJsonStructure([
            'access_token',
            'token_type',
            'expires_in',
        ]);
        /** @var string $token */
        $token = $response->json()['access_token'];

        return $token;
    }

    /**
     * @test
     *
     * @depends user_with_valid_credentials_can_login
     */
    public function authenticated_user_can_logout(string $accessToken): void
    {
        $response = $this->withHeader('Authorization', "Bearer $accessToken")->postJson('/api/auth/logout');

        $response->assertOk();
    }

    /**
     * @test
     *
     * @depends user_with_valid_credentials_can_login
     */
    public function authenticated_user_can_refresh_token(string $accessToken): void
    {
        $response = $this->withHeader('Authorization', "Bearer $accessToken")->postJson('/api/auth/refresh');

        $response->assertOk();
        $this->assertAuthenticated();
        $response->assertJsonStructure([
            'access_token',
            'token_type',
            'expires_in',
        ]);
    }

}
