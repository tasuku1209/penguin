<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Post;
use App\Models\Like;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LikeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        Post::all()->each(function ($post) use ($users) {
            $users->random(3)->each(function ($user) use ($post) {
                Like::factory()->create([
                    'post_id' => $post->id,
                    'user_id' => $user->id,
                ]);
            });
        });
    }
}
