<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            CategorySeeder::class,
            FacilitySeeder::class,
            CampusSeeder::class,
            PlatformSettingSeeder::class,
            DemoPropertySeeder::class,
        ]);
    }
}
