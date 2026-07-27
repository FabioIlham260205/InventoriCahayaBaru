<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/');

        $response->assertRedirect('/login');
    }

    public function test_login_redirects_to_google_oauth(): void
    {
        config()->set('inventory_auth.google.client_id', 'google-client-id');

        $response = $this->post('/login');

        $response->assertRedirectContains('https://accounts.google.com/o/oauth2/v2/auth');
        $response->assertRedirectContains('client_id=google-client-id');
        $response->assertRedirectContains('response_type=code');
    }

    public function test_google_oauth_callback_logs_user_in(): void
    {
        Http::fake([
            'https://oauth2.googleapis.com/token' => Http::response(['access_token' => 'token'], 200),
            'https://openidconnect.googleapis.com/v1/userinfo' => Http::response([
                'name' => 'Admin Cahaya',
                'email' => 'admin@example.com',
                'picture' => null,
            ], 200),
        ]);

        $response = $this
            ->withSession(['oauth_state' => 'valid-state'])
            ->get('/oauth/google/callback?code=valid-code&state=valid-state');

        $response->assertRedirect('/');
        $this->assertTrue(session('inventory_authenticated'));
        $this->assertSame('admin@example.com', session('inventory_user.email'));
    }
}
