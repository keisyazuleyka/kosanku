<?php

namespace Database\Factories;

use App\Enums\PropertyStatus;
use App\Enums\PropertyType;
use App\Models\Property;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Property>
 */
class PropertyFactory extends Factory
{
    public function definition(): array
    {
        $name = 'Kost '.fake()->unique()->streetName();

        return [
            'owner_id' => User::factory()->owner(),
            'name' => $name,
            'slug' => Str::slug($name).'-'.fake()->unique()->numerify('###'),
            'description' => fake()->paragraphs(3, true),
            'type' => fake()->randomElement(PropertyType::cases()),
            'city' => fake()->randomElement(['Depok', 'Bogor', 'Bandung', 'Jakarta Selatan']),
            'district' => fake()->citySuffix(),
            'address' => fake()->streetAddress(),
            'monthly_price' => fake()->numberBetween(800000, 3500000),
            'deposit' => 1000000,
            'additional_fees' => 'Listrik token, laundry opsional.',
            'rules' => 'Tidak merokok di dalam kamar. Tamu maks. pukul 21.00.',
            'rooms_total' => 10,
            'rooms_available' => 4,
            'status' => PropertyStatus::Approved,
        ];
    }
}
