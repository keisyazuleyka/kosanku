<?php

namespace Tests\Feature;

use App\Enums\PropertyStatus;
use App\Enums\UserRole;
use App\Models\Campus;
use App\Models\Property;
use App\Models\User;
use App\Notifications\AppNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CampusSearchAndRevisionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_campus_api_strictly_filters_by_city(): void
    {
        Campus::create([
            'city' => 'Depok',
            'name' => 'Universitas Indonesia (UI)',
            'short_name' => 'UI',
            'latitude' => -6.3686,
            'longitude' => 106.8341,
            'is_active' => true,
        ]);

        Campus::create([
            'city' => 'Bandung',
            'name' => 'Institut Teknologi Bandung (ITB)',
            'short_name' => 'ITB',
            'latitude' => -6.8915,
            'longitude' => 107.6107,
            'is_active' => true,
        ]);

        // Request with city=Depok should only return UI
        $resDepok = $this->getJson('/api/campuses?city=Depok');
        $resDepok->assertStatus(200);
        $resDepok->assertJsonCount(1);
        $resDepok->assertJsonFragment(['name' => 'Universitas Indonesia (UI)']);
        $resDepok->assertJsonMissing(['name' => 'Institut Teknologi Bandung (ITB)']);

        // Request without city should return empty array
        $resEmpty = $this->getJson('/api/campuses');
        $resEmpty->assertStatus(200);
        $resEmpty->assertJsonCount(0);
    }

    public function test_method_1_campus_search_filters_kost_by_radius(): void
    {
        $campus = Campus::create([
            'city' => 'Depok',
            'name' => 'Universitas Indonesia (UI)',
            'latitude' => -6.3686,
            'longitude' => 106.8341,
            'is_active' => true,
        ]);

        // Kost 1: ~1.2 km from UI (within 3km radius)
        $nearKost = Property::factory()->create([
            'name' => 'Kost Dekat UI Kukusan',
            'city' => 'Depok',
            'latitude' => -6.3620,
            'longitude' => 106.8280,
            'status' => PropertyStatus::Approved,
        ]);

        // Kost 2: in Bandung (~120 km away, definitely outside 10km radius)
        $farKost = Property::factory()->create([
            'name' => 'Kost Jauh di Dago Bandung',
            'city' => 'Bandung',
            'latitude' => -6.8900,
            'longitude' => 107.6100,
            'status' => PropertyStatus::Approved,
        ]);

        $response = $this->get('/kost?campus_id='.$campus->id.'&radius=5');
        $response->assertStatus(200);
        $response->assertSee('Kost Dekat UI Kukusan');
        $response->assertDontSee('Kost Jauh di Dago Bandung');
    }

    public function test_method_3_nearby_gps_search_filters_kost_by_coordinates(): void
    {
        // Property in Jakarta Selatan
        $jakselKost = Property::factory()->create([
            'name' => 'Kost Nyaman Blok M',
            'city' => 'Jakarta Selatan',
            'latitude' => -6.2440,
            'longitude' => 106.7980,
            'status' => PropertyStatus::Approved,
        ]);

        // Property in Surabaya
        $surabayaKost = Property::factory()->create([
            'name' => 'Kost Rungkut Surabaya',
            'city' => 'Surabaya',
            'latitude' => -7.3150,
            'longitude' => 112.7800,
            'status' => PropertyStatus::Approved,
        ]);

        // User GPS is in Blok M Jakarta
        $response = $this->get('/kost?lat=-6.2445&lng=106.7985&radius=3');
        $response->assertStatus(200);
        $response->assertSee('Kost Nyaman Blok M');
        $response->assertDontSee('Kost Rungkut Surabaya');
    }

    public function test_pending_property_never_appears_in_public_listings(): void
    {
        $approvedKost = Property::factory()->create([
            'name' => 'Kost Melati Indah Approved',
            'status' => PropertyStatus::Approved,
        ]);

        $pendingKost = Property::factory()->create([
            'name' => 'Kos Ganesha Nyaman Pending',
            'status' => PropertyStatus::Pending,
        ]);

        // Homepage check
        $homeRes = $this->get('/');
        $homeRes->assertStatus(200);
        $homeRes->assertSee('Kost Melati Indah Approved');
        $homeRes->assertDontSee('Kos Ganesha Nyaman Pending');

        // Kost listing page check
        $listingRes = $this->get('/kost');
        $listingRes->assertStatus(200);
        $listingRes->assertSee('Kost Melati Indah Approved');
        $listingRes->assertDontSee('Kos Ganesha Nyaman Pending');
    }

    public function test_pending_property_appears_in_admin_verification_and_can_be_approved(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);
        $owner = User::factory()->create(['role' => UserRole::Owner]);

        $pendingKost = Property::factory()->create([
            'name' => 'Kos Ganesha Nyaman Verification Test',
            'status' => PropertyStatus::Pending,
            'owner_id' => $owner->id,
        ]);

        // Admin sees pending listing in verification index
        $adminRes = $this->actingAs($admin)->get('/admin/verifikasi');
        $adminRes->assertStatus(200);
        $adminRes->assertSee('Kos Ganesha Nyaman Verification Test');

        // Admin approves the listing
        $approveRes = $this->actingAs($admin)->post("/admin/verifikasi/{$pendingKost->id}/approve");
        $approveRes->assertRedirect(route('admin.verifikasi.index'));

        $this->assertEquals(PropertyStatus::Approved, $pendingKost->fresh()->status);

        // Owner receives notification
        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $owner->id,
            'notifiable_type' => User::class,
        ]);
    }

    public function test_admin_can_archive_property(): void
    {
        $admin = User::factory()->create(['role' => UserRole::Admin]);
        $property = Property::factory()->create([
            'name' => 'Kost Yang Hendak Diarsipkan',
            'status' => PropertyStatus::Approved,
        ]);

        $archiveRes = $this->actingAs($admin)->post("/admin/kost/{$property->id}/archive");
        $archiveRes->assertRedirect();

        $this->assertEquals(PropertyStatus::Archived, $property->fresh()->status);
    }

    public function test_user_can_upload_avatar_and_update_profile(): void
    {
        Storage::fake('public');

        $user = User::factory()->create([
            'role' => UserRole::Tenant,
            'name' => 'Andi Santoso',
        ]);

        $file = UploadedFile::fake()->image('my-photo.jpg', 300, 300);

        $response = $this->actingAs($user)->put('/profil', [
            'name' => 'Andi Santoso Pratama',
            'email' => $user->email,
            'phone' => '081234567890',
            'city' => 'Depok',
            'address' => 'Jl. Margonda No. 12',
            'bio' => 'Mahasiswa tingkat akhir UI',
            'avatar' => $file,
        ]);

        $response->assertRedirect();
        $user->refresh();

        $this->assertEquals('Andi Santoso Pratama', $user->name);
        $this->assertEquals('Depok', $user->city);
        $this->assertNotNull($user->avatar_path);
        Storage::disk('public')->assertExists($user->avatar_path);
    }

    public function test_booking_creates_notifications_for_owner(): void
    {
        $owner = User::factory()->create(['role' => UserRole::Owner]);
        $tenant = User::factory()->create(['role' => UserRole::Tenant]);
        $property = Property::factory()->create([
            'owner_id' => $owner->id,
            'monthly_price' => 1000000,
            'deposit' => 200000,
            'rooms_total' => 5,
            'rooms_available' => 3,
            'status' => PropertyStatus::Approved,
        ]);

        $res = $this->actingAs($tenant)->post(route('bookings.store', $property), [
            'start_date' => now()->addDays(2)->toDateString(),
            'duration_months' => 3,
            'rooms_requested' => 1,
            'tenant_notes' => 'Tolong info cara check-in',
        ]);

        $res->assertRedirect();

        // Notification should exist for owner
        $this->assertDatabaseHas('notifications', [
            'notifiable_id' => $owner->id,
            'notifiable_type' => User::class,
        ]);
    }

    public function test_notifications_page_and_mark_as_read(): void
    {
        $user = User::factory()->create(['role' => UserRole::Tenant]);

        $user->notify(new AppNotification(
            title: 'Notifikasi Uji Coba',
            message: 'Ini adalah pesan pemberitahuan uji coba.',
            url: '/dashboard',
            type: 'info'
        ));

        $this->assertEquals(1, $user->unreadNotifications()->count());

        $pageRes = $this->actingAs($user)->get('/notifikasi');
        $pageRes->assertStatus(200);
        $pageRes->assertSee('Notifikasi Uji Coba');

        // Mark all as read
        $markRes = $this->actingAs($user)->post('/notifikasi/baca-semua');
        $markRes->assertRedirect();

        $this->assertEquals(0, $user->fresh()->unreadNotifications()->count());
    }
}
