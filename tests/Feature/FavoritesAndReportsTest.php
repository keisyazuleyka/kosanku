<?php

namespace Tests\Feature;

use App\Enums\PropertyStatus;
use App\Enums\ReportStatus;
use App\Enums\UserRole;
use App\Models\Favorite;
use App\Models\Property;
use App\Models\Report;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FavoritesAndReportsTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_toggle_favorites(): void
    {
        $user = User::factory()->create(['role' => UserRole::Tenant]);
        $property = Property::factory()->create(['status' => PropertyStatus::Approved]);

        // 1. Add to favorites
        $response1 = $this->actingAs($user)->post(route('favorites.toggle', $property));
        $response1->assertSessionHas('status', 'Kost disimpan ke favorit.');
        $this->assertDatabaseHas('favorites', [
            'user_id' => $user->id,
            'property_id' => $property->id,
        ]);

        // 2. Remove from favorites
        $response2 = $this->actingAs($user)->post(route('favorites.toggle', $property));
        $response2->assertSessionHas('status', 'Kost dihapus dari favorit.');
        $this->assertDatabaseMissing('favorites', [
            'user_id' => $user->id,
            'property_id' => $property->id,
        ]);
    }

    public function test_user_can_view_favorites_page(): void
    {
        $user = User::factory()->create(['role' => UserRole::Tenant]);
        $property = Property::factory()->create(['status' => PropertyStatus::Approved]);
        Favorite::query()->create(['user_id' => $user->id, 'property_id' => $property->id]);

        $response = $this->actingAs($user)->get(route('favorites.index'));

        $response->assertStatus(200);
        $response->assertSee($property->name);
    }

    public function test_tenant_can_report_property(): void
    {
        $user = User::factory()->create(['role' => UserRole::Tenant]);
        $property = Property::factory()->create(['status' => PropertyStatus::Approved]);

        $response = $this->actingAs($user)->post(route('reports.store', $property), [
            'reason' => 'Harga di aplikasi tidak sama dengan kenyataan di lapangan.',
        ]);

        $response->assertSessionHas('status');
        $this->assertDatabaseHas('reports', [
            'reporter_id' => $user->id,
            'property_id' => $property->id,
            'status' => ReportStatus::Pending->value,
        ]);
    }

    public function test_admin_can_resolve_report(): void
    {
        $admin = User::factory()->admin()->create();
        $reporter = User::factory()->create(['role' => UserRole::Tenant]);
        $property = Property::factory()->create(['status' => PropertyStatus::Approved]);

        $report = Report::query()->create([
            'reporter_id' => $reporter->id,
            'property_id' => $property->id,
            'reason' => 'Foto tidak sesuai kondisi riil.',
            'status' => ReportStatus::Pending,
        ]);

        $response = $this->actingAs($admin)->put(route('admin.reports.update', $report), [
            'status' => 'resolved',
            'admin_notes' => 'Sudah diverifikasi dan pemilik diperingatkan.',
        ]);

        $response->assertSessionHas('status');
        $this->assertEquals(ReportStatus::Resolved, $report->fresh()->status);
        $this->assertEquals('Sudah diverifikasi dan pemilik diperingatkan.', $report->fresh()->admin_notes);
    }
}
