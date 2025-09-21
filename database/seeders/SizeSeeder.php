<?php

namespace Database\Seeders;

use App\Models\Size;
use Illuminate\Database\Seeder;

class SizeSeeder extends Seeder
{
    public function run()
    {
        $sizes = [
            [
                'name' => ['en' => 'Small', 'ar' => 'صغير'],
                'price' => 7.99,
                'restaurant_id' => 1,
                'active' => true,
            ],
            [
                'name' => ['en' => 'Medium', 'ar' => 'متوسط'],
                'price' => 8.99,
                'restaurant_id' => 1,
                'active' => true,
            ],
            [
                'name' => ['en' => 'Large', 'ar' => 'كبير'],
                'price' => 9.99,
                'restaurant_id' => 1,
                'active' => true,
            ],
            [
                'name' => ['en' => 'Regular', 'ar' => 'عادي'],
                'price' => 14.99,
                'restaurant_id' => 1,
                'active' => true,
            ],
            [
                'name' => ['en' => 'Family Size', 'ar' => 'حجم عائلي'],
                'price' => 19.99,
                'restaurant_id' => 1,
                'active' => true,
            ],
        ];
        foreach ($sizes as $size) {
            Size::create($size);
        }
    }
}
