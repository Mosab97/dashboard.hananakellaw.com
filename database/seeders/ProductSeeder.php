<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    public function run()
    {
        DB::table('products')->insert([
            [
                'name' => json_encode(['en' => 'Burger', 'ar' => 'برجر']),
                'description' => json_encode(['en' => 'Delicious beef burger', 'ar' => 'برجر لحم لذيذ']),
                'image' => 'media/stock/food/img-2.jpg',
                'category_id' => 1,
                'restaurant_id' => 1,
                'price' => 9.99,
                'active' => true,
                'order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => json_encode(['en' => 'Pizza', 'ar' => 'بيتزا']),
                'description' => json_encode(['en' => 'Cheesy veggie pizza', 'ar' => 'بيتزا بالخضار والجبنة']),
                'image' => 'media/stock/food/img-1.jpg',
                'category_id' => 2,
                'restaurant_id' => 1,
                'price' => 14.99,
                'active' => true,
                'order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
