<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class RememberMeTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_with_remember_me_sets_remember_token(): void
    {
        $user = User::factory()->create(['password' => bcrypt('password')]);

        $this->post(route('login.submit'), [
            'username' => $user->username,
            'password' => 'password',
            'remember' => '1',
        ]);

        $this->assertAuthenticated();
        $user->refresh();
        $this->assertNotNull($user->remember_token);
    }

    public function test_login_without_remember_me_does_not_clear_remember_token(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
            'remember_token' => 'existing-token',
        ]);

        $this->post(route('login.submit'), [
            'username' => $user->username,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $user->refresh();
        $this->assertEquals('existing-token', $user->remember_token);
    }

    public function test_login_page_has_remember_checkbox(): void
    {
        $response = $this->get(route('login'));

        $response->assertSee('Ingat Saya');
        $response->assertSee('remember');
    }
}
