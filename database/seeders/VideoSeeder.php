<?php

namespace Database\Seeders;

use App\Models\Video;
use Illuminate\Database\Seeder;

class VideoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Video::create([
            'title' => 'Video 1',
            'description' => 'This is a test description',
            'path' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        ]);
        Video::create([
            'title' => 'Video 2',
            'description' => 'This is a test description',
            'path' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        ]);
    }
}
