<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_registration_validation_messages_are_hungarian(): void
    {
        $this->from('/register')->post('/register', [])->assertSessionHasErrors([
            'name' => 'Add meg a neved.',
            'email' => 'Add meg az e-mail-címed.',
            'password' => 'Add meg a jelszavad.',
        ]);
        $data = ['name' => 'Teszt', 'email' => 'test@example.com', 'password' => 'short', 'password_confirmation' => 'short'];
        $this->post('/register', $data)->assertSessionHasErrors([
            'password' => 'A jelszónak legalább 8 karakterből kell állnia.',
        ]);
        $this->post('/register', array_replace($data, ['password_confirmation' => 'different']))->assertSessionHasErrors([
            'password' => 'A jelszó és a megerősítése nem egyezik.',
        ]);
        \App\Models\User::factory()->create(['email' => $data['email']]);
        $this->post('/register', $data)->assertSessionHasErrors([
            'email' => 'Ezzel az e-mail-címmel már regisztráltak.',
        ]);
        $this->get('/register')->assertSee('Ezzel az e-mail-címmel már regisztráltak.');
        $this->assertGuest();
    }
}
