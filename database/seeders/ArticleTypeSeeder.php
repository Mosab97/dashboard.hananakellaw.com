<?php

namespace Database\Seeders;

use App\Models\ArticleType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ArticleTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ArticleType::create([
            'name' => ['he' => 'משפט', 'ar' => 'قانون '],
            'active' => true,
        ]);
        ArticleType::create([
            'name' => ['he' => 'מדריך', 'ar' => 'دليل'],
            'active' => true,
        ]);
    }
}
