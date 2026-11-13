<?php

namespace Tests\Feature;

use App\Enums\PropertyStatus;
use App\Enums\PropertyType;
use App\Enums\UserRole;
use App\Models\Facility;
use App\Models\Property;
use App\Models\PropertyImage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class OwnerPropertyTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_access_dashboard_and_property_list(): void
    {
        $owner = User::factory()->owner()->create();
        $property = Property::factory()->create(['owner_id' => $owner->id]);

        $response = $this->actingAs($owner)->get(route('owner.kost.index'));

        $response->assertStatus(200);
        $response->assertSee($property->name);
    }

    public function test_tenant_cannot_access_owner_dashboard(): void
    {
        $tenant = User::factory()->create(['role' => UserRole::Tenant]);

        $response = $this->actingAs($tenant)->get(route('owner.dashboard'));

        $response->assertStatus(403);
    }

    public function test_owner_can_create_new_property_with_images_and_facilities(): void
    {
        Storage::fake('public');

        $owner = User::factory()->owner()->create();
        $facility = Facility::query()->create(['name' => 'AC Dingin', 'slug' => 'ac-dingin', 'is_active' => true]);

        $response = $this->actingAs($owner)->post(route('owner.kost.store'), [
            'name' => 'Kost Permata Depok',
            'type' => PropertyType::Campur->value,
            'description' => 'Kost baru sangat nyaman, bersih dan strategis dekat stasiun.',
            'city' => 'Depok',
            'district' => 'Beji',
            'address' => 'Jl. Margonda Raya No. 10',
            'monthly_price' => 1500000,
            'deposit' => 1000000,
            'rooms_total' => 10,
            'rooms_available' => 5,
            'facilities' => [$facility->id],
            'submit_action' => 'pending',
            'images' => [
                UploadedFile::fake()->image('kamar1.jpg', 600, 400),
                UploadedFile::fake()->image('kamar2.jpg', 600, 400),
            ],
        ]);

        $response->assertRedirect(route('owner.kost.index'));

        $this->assertDatabaseHas('properties', [
            'owner_id' => $owner->id,
            'name' => 'Kost Permata Depok',
            'status' => PropertyStatus::Pending->value,
            'city' => 'Depok',
        ]);

        $property = Property::where('name', 'Kost Permata Depok')->first();
        $this->assertCount(1, $property->facilities);
        $this->assertCount(2, $property->images);
    }

    public function test_owner_can_update_own_property(): void
    {
        $owner = User::factory()->owner()->create();
        $property = Property::factory()->create([
            'owner_id' => $owner->id,
            'name' => 'Kost Lama',
            'rooms_total' => 10,
            'rooms_available' => 5,
            'monthly_price' => 1200000,
        ]);

        $response = $this->actingAs($owner)->put(route('owner.kost.update', $property), [
            'name' => 'Kost Baru Diupdate',
            'type' => PropertyType::Putri->value,
            'description' => 'Deskripsi update yang cukup panjang untuk melewati validasi.',
            'city' => 'Bandung',
            'address' => 'Jl. Dago No. 15',
            'monthly_price' => 1800000,
            'deposit' => 1000000,
            'rooms_total' => 12,
            'rooms_available' => 6,
            'submit_action' => 'pending',
        ]);

        $response->assertRedirect(route('owner.kost.index'));

        $this->assertDatabaseHas('properties', [
            'id' => $property->id,
            'name' => 'Kost Baru Diupdate',
            'monthly_price' => 1800000,
        ]);
    }

    public function test_owner_cannot_delete_other_owner_property(): void
    {
        $owner1 = User::factory()->owner()->create();
        $owner2 = User::factory()->owner()->create();

        $propertyOfOwner2 = Property::factory()->create(['owner_id' => $owner2->id]);

        $response = $this->actingAs($owner1)->delete(route('owner.kost.destroy', $propertyOfOwner2), [
            'confirm' => 'HAPUS',
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseHas('properties', ['id' => $propertyOfOwner2->id]);
    }

    public function test_owner_can_delete_own_property(): void
    {
        $owner = User::factory()->owner()->create();
        $property = Property::factory()->create(['owner_id' => $owner->id]);

        $response = $this->actingAs($owner)->delete(route('owner.kost.destroy', $property), [
            'confirm' => 'HAPUS',
        ]);

        $response->assertRedirect(route('owner.kost.index'));
        $this->assertDatabaseMissing('properties', ['id' => $property->id]);
    }

    public function test_owner_cannot_delete_other_owner_photo(): void
    {
        $owner1 = User::factory()->owner()->create();
        $owner2 = User::factory()->owner()->create();

        $property = Property::factory()->create(['owner_id' => $owner2->id]);
        $image = PropertyImage::query()->create([
            'property_id' => $property->id,
            'path' => 'properties/dummy.jpg',
            'is_external' => false,
            'sort_order' => 1,
            'is_cover' => true,
        ]);

        $response = $this->actingAs($owner1)->delete(route('owner.kost.images.destroy', [$property, $image]));

        $response->assertStatus(403);
        $this->assertDatabaseHas('property_images', ['id' => $image->id]);
    }
}
