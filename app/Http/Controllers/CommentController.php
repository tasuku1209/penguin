<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;

class CommentController extends Controller
{
     public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(StoreCommentRequest $request, Post $post)
    {
        $validated = $request->validated();

        $post->comments()->create([
            'body' => $validated['body'],
            'user_id' => auth()->id(), 
        ]);

        return redirect()->route('posts.show', $post);
    }

        public function destroy(Post $post, Comment $comment)
        {
            $this->authorize('delete', $comment);
            $comment->delete();
            return redirect()->route('posts.show', $post);
        }
}
