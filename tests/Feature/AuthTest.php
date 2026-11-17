<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Sign In');
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'admin@stagedecor.pk',
            'password' => bcrypt('password123'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        $response = $this->post('/login', [
            'email' => 'admin@stagedecor.pk',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    public function test_user_cannot_login_with_invalid_credentials(): void
    {
        User::factory()->create([
            'email' => 'admin@stagedecor.pk',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->from('/login')->post('/login', [
            'email' => 'admin@stagedecor.pk',
            'password' => 'wrong-password',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_inactive_user_cannot_login(): void
    {
        User::factory()->create([
            'email' => 'inactive@stagedecor.pk',
            'password' => bcrypt('password123'),
            'is_active' => false,
        ]);

        $response = $this->from('/login')->post('/login', [
            'email' => 'inactive@stagedecor.pk',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_user_can_logout(): void
    {
        $user = User::factory()->create(['is_active' => true]);

        $response = $this->actingAs($user)->post('/logout');

        $response->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_staff_cannot_access_admin_settings_or_user_management(): void
    {
        $staff = User::factory()->create([
            'role' => 'staff',
            'is_active' => true,
        ]);

        $this->actingAs($staff)->get('/settings/business')->assertStatus(403);
        $this->actingAs($staff)->get('/users')->assertStatus(403);
    }

    public function test_admin_can_access_admin_settings_and_user_management(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->actingAs($admin)->get('/settings/business')->assertStatus(200);
        $this->actingAs($admin)->get('/users')->assertStatus(200);
    }
}
