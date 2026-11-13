<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['Dekat Kampus', 'Kost dengan akses mudah ke universitas.'],
            ['Dekat Kantor', 'Cocok untuk pekerja dan professional muda.'],
            ['Eksklusif', 'Kamar lebih luas dengan fasilitas premium.'],
            ['Budget', 'Pilihan hemat untuk mahasiswa.'],
        ];

        foreach ($items as [$name, $description]) {
            Category::query()->updateOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name, 'description' => $description, 'is_active' => true]
            );
        }
    }
}
