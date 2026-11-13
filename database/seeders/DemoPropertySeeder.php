<?php

namespace Database\Seeders;

use App\Enums\PropertyStatus;
use App\Enums\PropertyType;
use App\Enums\UserRole;
use App\Models\Category;
use App\Models\Facility;
use App\Models\Property;
use App\Models\PropertyImage;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoPropertySeeder extends Seeder
{
    public function run(): void
    {
        $owners = [
            ['Raka Pratama', 'owner.raka@kosanku.test', 'Depok'],
            ['Sinta Ayu', 'owner.sinta@kosanku.test', 'Bandung'],
            ['Budi Santoso', 'owner.budi@kosanku.test', 'Jakarta Selatan'],
        ];

        $ownerModels = [];
        foreach ($owners as [$name, $email, $city]) {
            $owner = User::query()->firstOrNew(['email' => $email]);
            if (! $owner->exists) {
                $owner->password = Hash::make('password');
            }
            $owner->fill([
                'name' => $name,
                'city' => $city,
                'phone' => '081234567890',
                'is_active' => true,
            ]);
            $owner->role = UserRole::Owner;
            $owner->save();
            $ownerModels[$email] = $owner;
        }

        $tenant = User::query()->firstOrNew(['email' => 'penyewa@kosanku.test']);
        if (! $tenant->exists) {
            $tenant->password = Hash::make('password');
        }
        $tenant->fill([
            'name' => 'Nadia Putri',
            'city' => 'Depok',
            'phone' => '081298765432',
            'is_active' => true,
        ]);
        $tenant->role = UserRole::Tenant;
        $tenant->save();

        $admin = User::query()->firstOrNew(['email' => 'admin@kosanku.test']);
        if (! $admin->exists) {
            $admin->password = Hash::make('password');
        }
        $admin->fill([
            'name' => 'Admin Kosanku',
            'city' => 'Jakarta Selatan',
            'phone' => '081200000001',
            'is_active' => true,
        ]);
        $admin->role = UserRole::Admin;
        $admin->save();

        $categories = Category::query()->get()->keyBy('slug');
        $facilities = Facility::query()->pluck('id', 'slug');

        $listings = [
            [
                'owner' => 'owner.raka@kosanku.test',
                'name' => 'Kos Melati UI Depok',
                'city' => 'Depok',
                'district' => 'Beji',
                'village' => 'Kukusan',
                'address' => 'Jl. Margonda Raya No. 88, Beji',
                'latitude' => -6.368500,
                'longitude' => 106.829000,
                'type' => PropertyType::Putri,
                'category' => 'dekat-kampus',
                'price' => 1750000,
                'deposit' => 1750000,
                'featured' => true,
                'status' => PropertyStatus::Approved,
                'photos' => [
                    'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=1400&q=80',
                    'https://images.unsplash.com/photo-1505691938895-1758d7a238f1?auto=format&fit=crop&w=1400&q=80',
                ],
            ],
            [
                'owner' => 'owner.raka@kosanku.test',
                'name' => 'Kos Mawar Margonda',
                'city' => 'Depok',
                'district' => 'Pondok Cina',
                'village' => 'Pondok Cina',
                'address' => 'Jl. Tole Iskandar No. 12',
                'latitude' => -6.372000,
                'longitude' => 106.832000,
                'type' => PropertyType::Campur,
                'category' => 'budget',
                'price' => 1200000,
                'deposit' => 1000000,
                'featured' => true,
                'status' => PropertyStatus::Approved,
                'photos' => [
                    'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?auto=format&fit=crop&w=1400&q=80',
                ],
            ],
            [
                'owner' => 'owner.raka@kosanku.test',
                'name' => 'Kos Cendana Dramaga',
                'city' => 'Bogor',
                'district' => 'Dramaga',
                'village' => 'Dramaga',
                'address' => 'Jl. Raya Dramaga No. 45',
                'latitude' => -6.559000,
                'longitude' => 106.728000,
                'type' => PropertyType::Putra,
                'category' => 'dekat-kampus',
                'price' => 1100000,
                'deposit' => 1100000,
                'featured' => false,
                'status' => PropertyStatus::Approved,
                'photos' => [
                    'https://images.unsplash.com/photo-1493809842364-78817add7ffb?auto=format&fit=crop&w=1400&q=80',
                ],
            ],
            [
                'owner' => 'owner.sinta@kosanku.test',
                'name' => 'Kos Pinus IPB Bogor',
                'city' => 'Bogor',
                'district' => 'Babakan',
                'village' => 'Babakan',
                'address' => 'Jl. Babakan Raya No. 7',
                'latitude' => -6.556000,
                'longitude' => 106.724000,
                'type' => PropertyType::Putri,
                'category' => 'dekat-kampus',
                'price' => 1450000,
                'deposit' => 1450000,
                'featured' => true,
                'status' => PropertyStatus::Approved,
                'photos' => [
                    'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?auto=format&fit=crop&w=1400&q=80',
                ],
            ],
            [
                'owner' => 'owner.sinta@kosanku.test',
                'name' => 'Kos Dago Asri',
                'city' => 'Bandung',
                'district' => 'Dago',
                'village' => 'Dago',
                'address' => 'Jl. Ir. H. Djuanda No. 210',
                'latitude' => -6.885000,
                'longitude' => 107.615000,
                'type' => PropertyType::Campur,
                'category' => 'eksklusif',
                'price' => 2500000,
                'deposit' => 2500000,
                'featured' => true,
                'status' => PropertyStatus::Approved,
                'photos' => [
                    'https://images.unsplash.com/photo-1484154218962-a197022b5858?auto=format&fit=crop&w=1400&q=80',
                ],
            ],
            [
                'owner' => 'owner.sinta@kosanku.test',
                'name' => 'Kos Ganesha Nyaman',
                'city' => 'Bandung',
                'district' => 'Coblong',
                'village' => 'Lebak Siliwangi',
                'address' => 'Jl. Ganesha No. 15',
                'latitude' => -6.892000,
                'longitude' => 107.610000,
                'type' => PropertyType::Putra,
                'category' => 'dekat-kampus',
                'price' => 1600000,
                'deposit' => 1600000,
                'featured' => false,
                'status' => PropertyStatus::Pending, // Menunggu verifikasi admin (Listing pending milik Sinta)
                'photos' => [
                    'https://images.unsplash.com/photo-1554995207-c18c203602cb?auto=format&fit=crop&w=1400&q=80',
                ],
            ],
            [
                'owner' => 'owner.budi@kosanku.test',
                'name' => 'Kos Fatmawati Hijau',
                'city' => 'Jakarta Selatan',
                'district' => 'Cilandak',
                'village' => 'Gandaria Selatan',
                'address' => 'Jl. RS Fatmawati No. 33',
                'latitude' => -6.289000,
                'longitude' => 106.795000,
                'type' => PropertyType::Putri,
                'category' => 'dekat-kantor',
                'price' => 2800000,
                'deposit' => 2800000,
                'featured' => true,
                'status' => PropertyStatus::Approved,
                'photos' => [
                    'https://images.unsplash.com/photo-1616594039964-ae9021a400a4?auto=format&fit=crop&w=1400&q=80',
                ],
            ],
            [
                'owner' => 'owner.budi@kosanku.test',
                'name' => 'Kos Pondok Indah Studio',
                'city' => 'Jakarta Selatan',
                'district' => 'Pondok Indah',
                'village' => 'Pondok Pinang',
                'address' => 'Jl. Metro Pondok Indah No. 9',
                'latitude' => -6.275000,
                'longitude' => 106.782000,
                'type' => PropertyType::Campur,
                'category' => 'eksklusif',
                'price' => 3500000,
                'deposit' => 3500000,
                'featured' => true,
                'status' => PropertyStatus::Approved,
                'photos' => [
                    'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?auto=format&fit=crop&w=1400&q=80',
                ],
            ],
        ];

        foreach ($listings as $item) {
            $slug = Str::slug($item['name']);
            $property = Property::query()->updateOrCreate(
                ['slug' => $slug],
                [
                    'owner_id' => $ownerModels[$item['owner']]->id,
                    'category_id' => $categories[$item['category']]->id ?? null,
                    'name' => $item['name'],
                    'description' => 'DATA DEMO. '.$item['name'].' berada di '.$item['district'].', '.$item['city'].'. Kamar bersih, akses mudah, dan cocok untuk mahasiswa atau pekerja. Termasuk listrik token sesuai pemakaian. Pengajuan sewa melalui Kosanku belum termasuk pembayaran.',
                    'type' => $item['type'],
                    'city' => $item['city'],
                    'district' => $item['district'],
                    'village' => $item['village'] ?? null,
                    'address' => $item['address'],
                    'latitude' => $item['latitude'] ?? null,
                    'longitude' => $item['longitude'] ?? null,
                    'monthly_price' => $item['price'],
                    'deposit' => $item['deposit'],
                    'additional_fees' => 'Listrik token, air PDAM dibagi penghuni, laundry kiloan opsional.',
                    'rules' => "Tidak merokok di kamar.\nTamu lawan jenis maksimal pukul 21.00.\nJaga kebersihan dapur bersama.",
                    'rooms_total' => 12,
                    'rooms_available' => 5,
                    'status' => $item['status'] ?? PropertyStatus::Approved,
                    'is_featured' => $item['featured'],
                    'is_demo' => true,
                    'verified_at' => ($item['status'] ?? PropertyStatus::Approved) === PropertyStatus::Approved ? now() : null,
                ]
            );

            $property->facilities()->sync($facilities->random(min(6, $facilities->count()))->values()->all());

            $property->images()->delete();
            foreach ($item['photos'] as $index => $url) {
                PropertyImage::query()->create([
                    'property_id' => $property->id,
                    'path' => $url,
                    'is_external' => true,
                    'sort_order' => $index + 1,
                    'is_cover' => $index === 0,
                ]);
            }
        }
    }
}
