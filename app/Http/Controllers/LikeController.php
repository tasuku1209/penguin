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
            ->where('user_id', 1) // 認証実装までの仮のユーザーID
            ->first();
        
        if ($like) {
            $like->delete();
        } else {
            $post->likes()->create([
                'user_id' => 1, // 認証実装までの仮のユーザーID
            ]);
        }

        return redirect()->route('posts.show', $post);
    }
}
