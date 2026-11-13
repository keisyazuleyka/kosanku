<?php

namespace Tests\Feature;

use App\Enums\BookingStatus;
use App\Enums\PropertyStatus;
use App\Enums\UserRole;
use App\Models\Booking;
use App\Models\Property;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_tenant_can_submit_booking_request(): void
    {
        $tenant = User::factory()->create(['role' => UserRole::Tenant]);
        $owner = User::factory()->owner()->create();
        $property = Property::factory()->create([
            'owner_id' => $owner->id,
            'status' => PropertyStatus::Approved,
            'monthly_price' => 1500000,
            'deposit' => 1000000,
            'rooms_total' => 5,
            'rooms_available' => 3,
        ]);

        $response = $this->actingAs($tenant)->post(route('bookings.store', $property), [
            'start_date' => now()->addDays(7)->toDateString(),
            'duration_months' => 3,
            'rooms_requested' => 1,
            'tenant_notes' => 'Tolong sediakan kunci duplikat',
        ]);

        $booking = Booking::query()->first();
        $this->assertNotNull($booking);
        $response->assertRedirect(route('tenant.bookings.show', $booking));

        $this->assertEquals($tenant->id, $booking->tenant_id);
        $this->assertEquals($owner->id, $booking->owner_id);
        $this->assertEquals(BookingStatus::Pending, $booking->status);
        $this->assertEquals(1500000, $booking->monthly_price_snapshot);
        $this->assertEquals(1000000, $booking->deposit_snapshot);
        // Total = 1.500.000 * 3 bulan * 1 kamar + 1.000.000 deposit = 5.500.000
        $this->assertEquals(5500000, $booking->total_estimate);
    }

    public function test_cannot_book_more_than_available_rooms(): void
    {
        $tenant = User::factory()->create(['role' => UserRole::Tenant]);
        $property = Property::factory()->create([
            'status' => PropertyStatus::Approved,
            'rooms_available' => 1,
        ]);

        $response = $this->actingAs($tenant)->post(route('bookings.store', $property), [
            'start_date' => now()->addDays(3)->toDateString(),
            'duration_months' => 1,
            'rooms_requested' => 3,
        ]);

        $response->assertSessionHasErrors('rooms_requested');
        $this->assertDatabaseEmpty('bookings');
    }

    public function test_owner_can_approve_booking_and_room_availability_decrements(): void
    {
        $tenant = User::factory()->create(['role' => UserRole::Tenant]);
        $owner = User::factory()->owner()->create();
        $property = Property::factory()->create([
            'owner_id' => $owner->id,
            'status' => PropertyStatus::Approved,
            'rooms_available' => 5,
        ]);

        $booking = Booking::query()->create([
            'code' => 'KS-TEST0001',
            'tenant_id' => $tenant->id,
            'owner_id' => $owner->id,
            'property_id' => $property->id,
            'start_date' => now()->addDays(5),
            'duration_months' => 2,
            'end_date' => now()->addDays(65),
            'monthly_price_snapshot' => 1000000,
            'deposit_snapshot' => 500000,
            'total_estimate' => 2500000,
            'rooms_requested' => 2,
            'status' => BookingStatus::Pending,
        ]);

        $response = $this->actingAs($owner)->post(route('owner.bookings.approve', $booking), [
            'notes' => 'Silakan datang tanggal 5',
        ]);

        $response->assertSessionHas('status');
        $this->assertEquals(BookingStatus::Approved, $booking->fresh()->status);
        // Rooms available: 5 - 2 = 3
        $this->assertEquals(3, $property->fresh()->rooms_available);
    }

    public function test_tenant_can_cancel_pending_booking(): void
    {
        $tenant = User::factory()->create(['role' => UserRole::Tenant]);
        $owner = User::factory()->owner()->create();
        $property = Property::factory()->create(['owner_id' => $owner->id, 'status' => PropertyStatus::Approved]);

        $booking = Booking::query()->create([
            'code' => 'KS-TEST0002',
            'tenant_id' => $tenant->id,
            'owner_id' => $owner->id,
            'property_id' => $property->id,
            'start_date' => now()->addDays(5),
            'duration_months' => 1,
            'end_date' => now()->addDays(35),
            'monthly_price_snapshot' => 1000000,
            'deposit_snapshot' => 500000,
            'total_estimate' => 1500000,
            'rooms_requested' => 1,
            'status' => BookingStatus::Pending,
        ]);

        $response = $this->actingAs($tenant)->post(route('tenant.bookings.cancel', $booking), [
            'cancel_reason' => 'Dapat kost lain lebih dekat kampus',
        ]);

        $response->assertSessionHas('status');
        $this->assertEquals(BookingStatus::Cancelled, $booking->fresh()->status);
    }

    public function test_tenant_can_submit_review_on_completed_booking(): void
    {
        $tenant = User::factory()->create(['role' => UserRole::Tenant]);
        $owner = User::factory()->owner()->create();
        $property = Property::factory()->create(['owner_id' => $owner->id, 'status' => PropertyStatus::Approved]);

        $booking = Booking::query()->create([
            'code' => 'KS-TEST0003',
            'tenant_id' => $tenant->id,
            'owner_id' => $owner->id,
            'property_id' => $property->id,
            'start_date' => now()->subMonths(3),
            'duration_months' => 3,
            'end_date' => now()->subDay(),
            'monthly_price_snapshot' => 1000000,
            'deposit_snapshot' => 500000,
            'total_estimate' => 3500000,
            'rooms_requested' => 1,
            'status' => BookingStatus::Completed,
        ]);

        $response = $this->actingAs($tenant)->post(route('tenant.reviews.store', $booking), [
            'rating' => 5,
            'comment' => 'Kost sangat nyaman, pemilik ramah dan air selalu lancar.',
        ]);

        $response->assertSessionHas('status');
        $this->assertDatabaseHas('reviews', [
            'booking_id' => $booking->id,
            'user_id' => $tenant->id,
            'rating' => 5,
        ]);
    }

    public function test_tenant_can_submit_review_on_approved_booking(): void
    {
        $tenant = User::factory()->create(['role' => UserRole::Tenant]);
        $owner = User::factory()->owner()->create();
        $property = Property::factory()->create(['owner_id' => $owner->id, 'status' => PropertyStatus::Approved]);

        $booking = Booking::query()->create([
            'code' => 'KS-TEST0004',
            'tenant_id' => $tenant->id,
            'owner_id' => $owner->id,
            'property_id' => $property->id,
            'start_date' => now()->addDays(2),
            'duration_months' => 3,
            'end_date' => now()->addMonths(3),
            'monthly_price_snapshot' => 1200000,
            'deposit_snapshot' => 500000,
            'total_estimate' => 4100000,
            'rooms_requested' => 1,
            'status' => BookingStatus::Approved,
        ]);

        $response = $this->actingAs($tenant)->post(route('kost.reviews.store', $property), [
            'rating' => 5,
            'comment' => 'Tempatnya strategis sekali dan ibu kost sangat ramah!',
        ]);

        $response->assertSessionHas('status');
        $this->assertDatabaseHas('reviews', [
            'booking_id' => $booking->id,
            'property_id' => $property->id,
            'user_id' => $tenant->id,
            'rating' => 5,
        ]);
    }

    public function test_tenant_without_booking_cannot_submit_review(): void
    {
        $tenant = User::factory()->create(['role' => UserRole::Tenant]);
        $owner = User::factory()->owner()->create();
        $property = Property::factory()->create(['owner_id' => $owner->id, 'status' => PropertyStatus::Approved]);

        $response = $this->actingAs($tenant)->post(route('kost.reviews.store', $property), [
            'rating' => 5,
            'comment' => 'Saya belum pernah tinggal disini.',
        ]);

        $response->assertSessionHasErrors('review');
        $this->assertDatabaseMissing('reviews', [
            'property_id' => $property->id,
            'user_id' => $tenant->id,
        ]);
    }
}
