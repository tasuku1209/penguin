<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCommentRequest $request, Post $post)
    {
        $validated = $request->validated();

        //$validated['user_id'] = 1; // 認証実装までの仮のユーザーID

        $post->comments()->create([
            'body' => $validated['body'],
            'user_id' => 1, // 認証実装までの仮のユーザーID
        ]);

        return redirect()->route('posts.show', $post);
    }
}
