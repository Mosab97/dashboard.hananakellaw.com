<?php

namespace Database\Seeders;

use App\Models\Slider;
use Illuminate\Database\Seeder;

class SliderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Slider::create([
            'restaurant_id' => 1,
            'title' => ['en' => 'Slider 1', 'ar' => 'Slider 1'],
            'description' => ['en' => 'Description 1', 'ar' => 'Description 1'],
            'image' => 'media/mobile-app/sliders/onboarding01.png',
            'link' => 'https://www.google.com',
        ]);

        Slider::create([
            'restaurant_id' => 1,
            'title' => ['en' => 'Slider 2', 'ar' => 'Slider 2'],
            'description' => ['en' => 'Description 2', 'ar' => 'Description 2'],
            'image' => 'media/mobile-app/sliders/onboarding02.png',
            'link' => 'https://www.google.com',
        ]);
    }
}
