<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesAndAdminSeeder::class,
            ProvinceCitySeeder::class,
            ColorSeeder::class,
            ShipmentSeeder::class,
            BrandSeeder::class,
            CategorySeeder::class,
            WarrantySeeder::class,
            SettingsSeeder::class,
        ]);
    }
}
