<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_books_without_bearer_token_returns_401(): void
    {
        $response = $this->getJson('/api/books');

        $response
            ->assertUnauthorized()
            ->assertJson([
                'message' => 'Unauthenticated.',
            ]);
    }

    public function test_login_with_wrong_credentials_returns_401(): void
    {
        // User::factory()->create([
        //     'email' => 'user@example.com',
        //     'password' => bcrypt('correct-password'),
        // ]);

        $response = $this->postJson('/api/login', [
            'email' => 'user@example.com',
            'password' => 'wrong-password',
        ]);

        $response
            ->assertUnauthorized()
            ->assertJson([
                'message' => 'Unauthorized',
            ]);
    }
}
