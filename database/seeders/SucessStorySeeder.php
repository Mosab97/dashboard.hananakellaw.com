<?php

namespace Database\Seeders;

use App\Models\SucessStory;
use Illuminate\Database\Seeder;

class SucessStorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SucessStory::create([
            'owner_name' => ['en' => 'John Doe', 'ar' => 'جون دو'],
            'rate' => 4,
            'description' => ['en' => 'This is a test description', 'ar' => 'هذا هو وصف الاختبار'],

        ]);
        SucessStory::create([
            'owner_name' => ['en' => 'Jane Doe', 'ar' => 'جين دو'],
            'rate' => 5,
            'description' => ['en' => 'This is a test description', 'ar' => 'هذا هو وصف الاختبار'],
        ]);
        SucessStory::create([
            'owner_name' => ['en' => 'John Doe', 'ar' => 'جون دو'],
            'rate' => 3,
            'description' => ['en' => 'This is a test description', 'ar' => 'هذا هو وصف الاختبار'],
        ]);
        SucessStory::create([
            'owner_name' => ['en' => 'John Doe', 'ar' => 'جون دو'],
            'rate' => 2,
            'description' => ['en' => 'This is a test description', 'ar' => 'هذا هو وصف الاختبار'],
        ]);
    }
}
