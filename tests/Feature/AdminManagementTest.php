<?php

namespace Tests\Feature;

use App\Enums\PropertyStatus;
use App\Enums\UserRole;
use App\Models\Category;
use App\Models\Facility;
use App\Models\Property;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_dashboard_overview(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Overview admin');
    }

    public function test_non_admin_cannot_access_admin_dashboard(): void
    {
        $tenant = User::factory()->create(['role' => UserRole::Tenant]);
        $owner = User::factory()->owner()->create();

        $this->actingAs($tenant)->get(route('admin.dashboard'))->assertStatus(403);
        $this->actingAs($owner)->get(route('admin.dashboard'))->assertStatus(403);
    }

    public function test_admin_can_approve_pending_property(): void
    {
        $admin = User::factory()->admin()->create();
        $property = Property::factory()->create(['status' => PropertyStatus::Pending]);

        $response = $this->actingAs($admin)->post(route('admin.verifikasi.approve', $property));

        $response->assertRedirect(route('admin.verifikasi.index'));

        $this->assertDatabaseHas('properties', [
            'id' => $property->id,
            'status' => PropertyStatus::Approved->value,
            'verified_by' => $admin->id,
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'actor_id' => $admin->id,
            'action' => 'property.approved',
        ]);
    }

    public function test_admin_can_reject_property_with_reason(): void
    {
        $admin = User::factory()->admin()->create();
        $property = Property::factory()->create(['status' => PropertyStatus::Pending]);

        $response = $this->actingAs($admin)->post(route('admin.verifikasi.reject', $property), [
            'rejection_reason' => 'Foto kost buram dan alamat tidak lengkap.',
        ]);

        $response->assertRedirect(route('admin.verifikasi.index'));

        $this->assertDatabaseHas('properties', [
            'id' => $property->id,
            'status' => PropertyStatus::Rejected->value,
            'rejection_reason' => 'Foto kost buram dan alamat tidak lengkap.',
        ]);
    }

    public function test_admin_can_toggle_user_status_but_not_self(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create(['is_active' => true]);

        // Toggle user to inactive
        $response = $this->actingAs($admin)->post(route('admin.users.toggle', $user));
        $response->assertSessionHas('status', 'Akun ditangguhkan.');
        $this->assertFalse($user->fresh()->is_active);

        // Cannot suspend self
        $responseSelf = $this->actingAs($admin)->post(route('admin.users.toggle', $admin));
        $responseSelf->assertSessionHasErrors('user');
        $this->assertTrue($admin->fresh()->is_active);
    }

    public function test_admin_can_manage_categories_and_facilities(): void
    {
        $admin = User::factory()->admin()->create();

        // Create category
        $this->actingAs($admin)->post(route('admin.categories.store'), [
            'name' => 'Dekat Rumah Sakit',
            'description' => 'Strategis untuk dokter dan perawat',
        ])->assertSessionHas('status');

        $this->assertDatabaseHas('categories', ['name' => 'Dekat Rumah Sakit']);

        // Create facility
        $this->actingAs($admin)->post(route('admin.facilities.store'), [
            'name' => 'Water Heater',
            'icon' => 'droplet',
        ])->assertSessionHas('status');

        $this->assertDatabaseHas('facilities', ['name' => 'Water Heater']);
    }

    public function test_admin_can_update_platform_settings(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->put(route('admin.settings.update'), [
            'site_name' => 'Kosanku Indonesia',
            'tagline' => 'Find your space. Feel at home.',
            'contact_email' => 'admin@kosanku.test',
            'contact_phone' => '081234567890',
            'min_listing_price' => 400000,
            'how_it_works' => 'Panduan cara kerja baru.',
            'terms' => 'Syarat dan ketentuan terbaru.',
            'privacy' => 'Kebijakan privasi terupdate.',
        ]);

        $response->assertSessionHas('status');
        $this->assertDatabaseHas('platform_settings', [
            'key' => 'site_name',
            'value' => 'Kosanku Indonesia',
        ]);
    }
}
