<?php

namespace Tests\Feature;

use App\Enums\UserRole;
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

    public function test_register_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_users_can_register_as_tenant(): void
    {
        $response = $this->post('/register', [
            'name' => 'Calon Penyewa',
            'email' => 'penyewa.baru@example.com',
            'phone' => '081234567890',
            'role' => 'tenant',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('tenant.dashboard'));

        $this->assertDatabaseHas('users', [
            'email' => 'penyewa.baru@example.com',
            'role' => UserRole::Tenant->value,
        ]);
    }

    public function test_users_can_register_as_owner(): void
    {
        $response = $this->post('/register', [
            'name' => 'Calon Pemilik Kost',
            'email' => 'pemilik.baru@example.com',
            'phone' => '081234567891',
            'role' => 'owner',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('owner.dashboard'));

        $this->assertDatabaseHas('users', [
            'email' => 'pemilik.baru@example.com',
            'role' => UserRole::Owner->value,
        ]);
    }

    public function test_users_cannot_register_as_admin(): void
    {
        $response = $this->post('/register', [
            'name' => 'Hacker Admin',
            'email' => 'hacker@example.com',
            'role' => 'admin',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('role');
        $this->assertGuest();
        $this->assertDatabaseMissing('users', [
            'email' => 'hacker@example.com',
        ]);
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::Tenant,
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('tenant.dashboard'));
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }

    public function test_suspended_users_cannot_access_dashboard(): void
    {
        $user = User::factory()->create([
            'is_active' => false,
            'role' => UserRole::Tenant,
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_users_can_authenticate_via_ajax_modal(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::Tenant,
        ]);

        $response = $this->postJson('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);
    }

    public function test_ajax_modal_login_validation_failure(): void
    {
        $response = $this->postJson('/login', [
            'email' => 'wrong@example.com',
            'password' => 'wrong-pass',
        ]);

        $this->assertGuest();
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_users_can_register_via_ajax_modal(): void
    {
        $response = $this->postJson('/register', [
            'name' => 'Modal User',
            'email' => 'modal@example.com',
            'phone' => '081299998888',
            'role' => 'tenant',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $this->assertAuthenticated();
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'modal@example.com',
        ]);
    }
}
