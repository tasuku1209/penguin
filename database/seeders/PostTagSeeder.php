<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\Tag;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PostTagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = Tag::all();
        Post::all()->each(function ($post) use ($tags) {
            $selectedTags = $tags->random(rand(1, 3));
            $post->tags()->attach($selectedTags->pluck('id'));
        });
    }
}
