<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSizeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('product_sizes')->insert([
            [
                'product_id' => 1,
                'size_id' => 1,
                'price' => 10,
            ],
        ]);

        DB::table('product_sizes')->insert([
            [
                'product_id' => 1,
                'size_id' => 2,
                'price' => 15,
            ],
        ]);
    }
}
