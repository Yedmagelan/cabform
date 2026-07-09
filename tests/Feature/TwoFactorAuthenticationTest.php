<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class TwoFactorAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_without_2fa_logs_in_directly(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
            'two_factor_enabled' => false,
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('learner.dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_user_with_2fa_is_redirected_to_two_factor_page(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
            'two_factor_enabled' => true,
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('two-factor.index'));
        $this->assertGuest();

        $this->assertTrue(session()->has('2fa_user_id'));
        $this->assertTrue(session()->has('2fa_code'));
    }

    public function test_user_can_verify_2fa_with_correct_code(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
            'two_factor_enabled' => true,
        ]);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $code = session('2fa_code');

        $response = $this->post('/two-factor', [
            'code' => (string) $code,
        ]);

        $response->assertRedirect(route('learner.dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_user_fails_2fa_with_incorrect_code(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
            'two_factor_enabled' => true,
        ]);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response = $this->post('/two-factor', [
            'code' => '000000', // incorrect code
        ]);

        $response->assertSessionHasErrors('code');
        $this->assertGuest();
    }
}
