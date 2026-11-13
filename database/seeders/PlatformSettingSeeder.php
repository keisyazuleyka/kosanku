<?php

namespace Database\Seeders;

use App\Models\PlatformSetting;
use Illuminate\Database\Seeder;

class PlatformSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['site_name', 'Kosanku', 'general'],
            ['tagline', 'Find your space. Feel at home.', 'general'],
            ['contact_email', 'halo@kosanku.test', 'general'],
            ['contact_phone', '0812-0000-0000', 'general'],
            ['min_listing_price', '500000', 'general'],
            ['how_it_works', 'Cari kost, ajukan sewa, pemilik konfirmasi. Tanpa pembayaran online pada versi ini.', 'content'],
            ['terms', 'Dengan menggunakan Kosanku, Anda setuju untuk memberikan data yang akurat dan mematuhi aturan listing.', 'content'],
            ['privacy', 'Kosanku menyimpan data akun dan pengajuan sewa di MySQL lokal Anda. Jangan bagikan password.', 'content'],
        ];

        foreach ($settings as [$key, $value, $group]) {
            PlatformSetting::query()->updateOrCreate(
                ['key' => $key],
                ['value' => $value, 'group' => $group]
            );
        }
    }
}
