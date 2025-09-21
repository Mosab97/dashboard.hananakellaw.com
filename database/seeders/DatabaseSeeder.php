<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $this->call([
            SettingsSeeder::class,
            ConstantsTableSeederV3::class,
            RolesAndPermissionsSeeder::class,
            AdminSeeder::class,
            MenuSeeder::class,

            // RestaurantSeeder::class,
            // CategorySeeder::class,
            // ProductSeeder::class,
            // SizeSeeder::class,
            // ProductSizeSeeder::class,
            // SliderSeeder::class,
            // SucessStorySeeder::class,
            // VideoSeeder::class,
        ]);
    }
}
