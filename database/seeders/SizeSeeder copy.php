<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SizeSeeder extends Seeder
{
    public function run()
    {
        DB::table('sizes')->insert([
            [
                'name' => json_encode(['en' => 'Small', 'ar' => 'صغير']),
                'price' => 7.99,
                'restaurant_id' => 1,
                'active' => true,
                'order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => json_encode(['en' => 'Medium', 'ar' => 'متوسط']),
                'price' => 8.99,
                'restaurant_id' => 1,
                'active' => true,
                'order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => json_encode(['en' => 'Large', 'ar' => 'كبير']),
                'price' => 9.99,
                'restaurant_id' => 1,
                'active' => true,
                'order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => json_encode(['en' => 'Regular', 'ar' => 'عادي']),
                'price' => 14.99,
                'restaurant_id' => 1,
                'active' => true,
                'order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => json_encode(['en' => 'Family Size', 'ar' => 'حجم عائلي']),
                'price' => 19.99,
                'restaurant_id' => 1,
                'active' => true,
                'order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
