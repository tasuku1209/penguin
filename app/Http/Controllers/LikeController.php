<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\Post;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    public function store(Request $request, Post $post)
    {
        $like = Like::where('post_id', $post->id)
            ->where('user_id', auth()->id())
            ->first();
        
        if ($like) {
            $like->delete();
        } else {
            $post->likes()->create([
                'user_id' => auth()->id(),
            ]);
        }

        return redirect()->route('posts.show', $post);
    }
}
