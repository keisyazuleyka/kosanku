<?php

namespace Database\Seeders;

use App\Models\Facility;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class FacilitySeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            'Wi-Fi', 'AC', 'Kamar Mandi Dalam', 'Kasur', 'Lemari', 'Meja Belajar',
            'Parkir Motor', 'Parkir Mobil', 'Dapur Bersama', 'Laundry', 'CCTV', 'Security 24 Jam',
        ];

        foreach ($items as $name) {
            Facility::query()->updateOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name, 'is_active' => true]
            );
        }
    }
}
