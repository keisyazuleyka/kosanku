<?php

namespace Tests\Feature;

use App\Enums\PropertyStatus;
use App\Enums\UserRole;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Property;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChatAndLocationTest extends TestCase
{
    use RefreshDatabase;

    public function test_location_autocomplete_returns_matching_cities_and_districts(): void
    {
        Property::factory()->create([
            'city' => 'Jakarta Selatan',
            'district' => 'Kebayoran Baru',
            'status' => PropertyStatus::Approved,
        ]);

        Property::factory()->create([
            'city' => 'Depok',
            'district' => 'Beji',
            'status' => PropertyStatus::Approved,
        ]);

        $response = $this->getJson('/api/locations?q=Jakarta');
        $response->assertStatus(200);
        $response->assertJsonFragment(['label' => 'Jakarta Selatan']);

        $response2 = $this->getJson('/api/locations?q=Beji');
        $response2->assertStatus(200);
        $response2->assertJsonFragment(['type' => 'Kecamatan']);
    }

    public function test_tenant_can_view_dedicated_booking_page(): void
    {
        $tenant = User::factory()->create(['role' => UserRole::Tenant]);
        $property = Property::factory()->create([
            'status' => PropertyStatus::Approved,
            'slug' => 'kost-mangga-dua',
        ]);

        $response = $this->actingAs($tenant)->get('/kost/kost-mangga-dua/booking');
        $response->assertStatus(200);
        $response->assertSee('Ajukan Booking');
        $response->assertSee($property->name);
    }

    public function test_tenant_can_start_chat_with_owner_and_send_messages(): void
    {
        $owner = User::factory()->create(['role' => UserRole::Owner]);
        $tenant = User::factory()->create(['role' => UserRole::Tenant]);
        $property = Property::factory()->create([
            'owner_id' => $owner->id,
            'status' => PropertyStatus::Approved,
        ]);

        // Start chat
        $startResponse = $this->actingAs($tenant)->post(route('chat.start', $property));
        $startResponse->assertRedirect();

        $conversation = Conversation::first();
        $this->assertNotNull($conversation);
        $this->assertEquals($tenant->id, $conversation->tenant_id);
        $this->assertEquals($owner->id, $conversation->owner_id);

        // Send a reply
        $sendResponse = $this->actingAs($tenant)->postJson(route('chat.messages.send', $conversation), [
            'content' => 'Bolehkah saya survei lokasi besok sore?',
        ]);

        $sendResponse->assertStatus(200);
        $sendResponse->assertJson(['success' => true]);

        $this->assertDatabaseHas('messages', [
            'conversation_id' => $conversation->id,
            'sender_id' => $tenant->id,
            'content' => 'Bolehkah saya survei lokasi besok sore?',
        ]);

        // Poll messages as owner
        $pollResponse = $this->actingAs($owner)->getJson("/api/chat/{$conversation->id}/messages");
        $pollResponse->assertStatus(200);
        $this->assertCount(2, $pollResponse->json('messages')); // initial message + sent reply
    }
}
