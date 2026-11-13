<?php

namespace Tests\Feature;

use App\Enums\PropertyStatus;
use App\Models\Category;
use App\Models\Property;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_renders_successfully_with_data(): void
    {
        $category = Category::query()->create(['name' => 'Dekat Kampus', 'slug' => 'dekat-kampus', 'is_active' => true]);
        $property = Property::factory()->create([
            'category_id' => $category->id,
            'status' => PropertyStatus::Approved,
            'is_featured' => true,
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee($property->name);
        $response->assertSee('Dekat Kampus');
        $response->assertSee('Kosanku');
    }

    public function test_property_listing_page_filters_by_city_and_type(): void
    {
        $depokProp = Property::factory()->create([
            'name' => 'Kost UI Depok Asri',
            'city' => 'Depok',
            'status' => PropertyStatus::Approved,
            'monthly_price' => 1500000,
        ]);

        $bandungProp = Property::factory()->create([
            'name' => 'Kost Dago Bandung Dingin',
            'city' => 'Bandung',
            'status' => PropertyStatus::Approved,
            'monthly_price' => 2500000,
        ]);

        $response = $this->get('/kost?city=Depok');
        $response->assertStatus(200);
        $response->assertSee('Kost UI Depok Asri');
        $response->assertDontSee('Kost Dago Bandung Dingin');
    }

    public function test_public_can_view_approved_kost_detail(): void
    {
        $property = Property::factory()->create([
            'name' => 'Kost Eksklusif Melati',
            'slug' => 'kost-eksklusif-melati',
            'status' => PropertyStatus::Approved,
            'monthly_price' => 2000000,
            'deposit' => 1000000,
        ]);

        $response = $this->get('/kost/kost-eksklusif-melati');

        $response->assertStatus(200);
        $response->assertSee('Kost Eksklusif Melati');
        $response->assertSee('Rp 2.000.000');
    }

    public function test_guest_cannot_view_unapproved_kost_detail(): void
    {
        $property = Property::factory()->create([
            'name' => 'Kost Rahasia Draft',
            'slug' => 'kost-rahasia-draft',
            'status' => PropertyStatus::Draft,
        ]);

        $response = $this->get('/kost/kost-rahasia-draft');

        $response->assertStatus(403);
    }

    public function test_static_information_pages_render(): void
    {
        $this->get('/cara-kerja')->assertStatus(200);
        $this->get('/syarat-ketentuan')->assertStatus(200);
        $this->get('/kebijakan-privasi')->assertStatus(200);
    }
}
