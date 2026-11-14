<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }

    public function test_login_validation_and_failed_credentials_are_hungarian(): void
    {
        $this->post('/login', [])->assertSessionHasErrors([
            'email' => 'Add meg az e-mail-címed.', 'password' => 'Add meg a jelszavad.',
        ]);
        $this->post('/login', ['email' => 'invalid', 'password' => 'password'])->assertSessionHasErrors([
            'email' => 'Adj meg egy érvényes e-mail-címet.',
        ]);
        $this->from('/login')->post('/login', ['email' => 'missing@example.com', 'password' => 'wrong'])->assertSessionHasErrors([
            'email' => 'Hibás e-mail-cím vagy jelszó.',
        ]);
        $this->get('/login')->assertSee('Hibás e-mail-cím vagy jelszó.');
        $this->assertGuest();
    }

    public function test_login_rate_limit_message_is_hungarian(): void
    {
        $key = 'limited@example.com|127.0.0.1';
        \Illuminate\Support\Facades\RateLimiter::clear($key);
        for ($i = 0; $i < 5; $i++) {
            \Illuminate\Support\Facades\RateLimiter::hit($key, 60);
        }
        $this->post('/login', ['email' => 'limited@example.com', 'password' => 'wrong'])->assertSessionHasErrors('email');
        $message = session('errors')->first('email');
        $this->assertMatchesRegularExpression('/^Túl sok bejelentkezési kísérlet\. Próbáld újra \d+ másodperc múlva\.$/u', $message);
        \Illuminate\Support\Facades\RateLimiter::clear($key);
        $this->assertGuest();
    }
}
