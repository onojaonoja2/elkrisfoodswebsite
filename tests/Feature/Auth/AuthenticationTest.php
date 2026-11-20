<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Laravel\Fortify\Features;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered()
    {
        $response = $this->get(route('login'));

        $response->assertOk();
    }

    public function test_users_can_authenticate_using_the_login_screen()
    {
        $user = User::factory()->create();

        $response = $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_admins_are_redirected_to_the_admin_panel_after_login()
    {
        $admin = User::factory()->admin()->create();

        $response = $this->post(route('login.store'), [
            'email' => $admin->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/admin');
    }

    public function test_admins_receive_an_inertia_location_response_after_login()
    {
        $admin = User::factory()->admin()->create();

        $response = $this
            ->withHeader('X-Inertia', 'true')
            ->post(route('login.store'), [
                'email' => $admin->email,
                'password' => 'password',
            ]);

        $this->assertAuthenticated();
        $response->assertStatus(409);
        $response->assertHeader('X-Inertia-Location', '/admin');
    }

    public function test_admins_are_redirected_to_an_intended_admin_url_using_an_inertia_location_response()
    {
        $admin = User::factory()->admin()->create();

        $response = $this
            ->withSession(['url.intended' => '/admin/products'])
            ->withHeader('X-Inertia', 'true')
            ->post(route('login.store'), [
                'email' => $admin->email,
                'password' => 'password',
            ]);

        $this->assertAuthenticated();
        $response->assertStatus(409);
        $response->assertHeader('X-Inertia-Location', '/admin/products');
        $response->assertSessionMissing('url.intended');
    }

    public function test_editors_are_redirected_to_the_content_studio_after_login()
    {
        $editor = User::factory()->editor()->create();

        $response = $this->post(route('login.store'), [
            'email' => $editor->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/studio');
    }

    public function test_authors_are_redirected_to_the_content_studio_after_login()
    {
        $author = User::factory()->author()->create();

        $response = $this->post(route('login.store'), [
            'email' => $author->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/studio');
    }

    public function test_editors_are_not_redirected_to_an_intended_admin_url()
    {
        $editor = User::factory()->editor()->create();

        $response = $this
            ->withSession(['url.intended' => '/admin/products'])
            ->post(route('login.store'), [
                'email' => $editor->email,
                'password' => 'password',
            ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/studio');
        $response->assertSessionMissing('url.intended');
    }

    public function test_content_staff_are_redirected_to_an_intended_studio_url()
    {
        $editor = User::factory()->editor()->create();

        $response = $this
            ->withHeader('X-Inertia', 'true')
            ->withSession(['url.intended' => '/studio/site-content'])
            ->post(route('login.store'), [
                'email' => $editor->email,
                'password' => 'password',
            ]);

        $this->assertAuthenticated();
        $response->assertStatus(409);
        $response->assertHeader('X-Inertia-Location', '/studio/site-content');
        $response->assertSessionMissing('url.intended');
    }

    public function test_non_admins_are_not_redirected_to_an_intended_admin_url()
    {
        $user = User::factory()->create();

        $response = $this
            ->withSession(['url.intended' => '/admin'])
            ->post(route('login.store'), [
                'email' => $user->email,
                'password' => 'password',
            ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_users_with_two_factor_enabled_are_redirected_to_two_factor_challenge()
    {
        $this->skipUnlessFortifyFeature(Features::twoFactorAuthentication());

        Features::twoFactorAuthentication([
            'confirm' => true,
            'confirmPassword' => true,
        ]);

        $user = User::factory()->create();

        $user->forceFill([
            'two_factor_secret' => encrypt('test-secret'),
            'two_factor_recovery_codes' => encrypt(json_encode(['code1', 'code2'])),
            'two_factor_confirmed_at' => now(),
        ])->save();

        $response = $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('two-factor.login'));
        $response->assertSessionHas('login.id', $user->id);
        $this->assertGuest();
    }

    public function test_users_can_not_authenticate_with_invalid_password()
    {
        $user = User::factory()->create();

        $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_logout()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('logout'));

        $this->assertGuest();
        $response->assertRedirect(route('home'));
    }

    public function test_users_are_rate_limited()
    {
        $user = User::factory()->create();

        RateLimiter::increment(md5('login'.implode('|', [$user->email, '127.0.0.1'])), amount: 5);

        $response = $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $response->assertTooManyRequests();
    }
}
