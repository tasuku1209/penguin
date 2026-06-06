<?php

namespace App\Http\Controllers\Api;

use App\Models\Post;
use App\Http\Resources\PostResource;
use App\Http\Requests\StorePostRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $posts = Post::with('user', 'tags')
            ->withCount(['comments', 'likes']);

        if ($request->filled('keyword')) {
            $posts->where(function ($query) use ($request) {
                 $query->where('title', 'like', '%' . $request->keyword . '%')
                    ->orWhere('body', 'like', '%' . $request->keyword . '%');
            });
        }

        if ($request->filled('tags')) {
            $posts->whereHas('tags', function ($query) use ($request) {
                $query->whereIn('id', $request->tags);
            });
        }

        if ($request->sort === 'latest') {
            $posts->orderBy('created_at', 'desc');
        } elseif ($request->sort === 'oldest') {
            $posts->orderBy('created_at', 'asc');
        } elseif ($request->sort === 'likes') {
            $posts->orderBy('likes_count', 'desc');
        } elseif ($request->sort === 'comments') {
            $posts->orderBy('comments_count', 'desc');
        } else {
            $posts->latest();
        }

        $posts = $posts->paginate(10)
            ->withQueryString();

        return PostResource::collection($posts);
    }

    public function show(Post $post)
    {
        $post->load([
            'user',
            'tags',
            'comments.user',
        ])->loadCount(['comments', 'likes']);
        return PostResource::make($post);
    }

    public function store(StorePostRequest $request)
    {
        $validated = $request->validated();
        $validated['user_id'] = auth()->id(); 
        $post = Post::create($validated);
        $post->tags()->attach($request->input('tags', []));

        return response()->json([
            'message' => 'Post created successfully',
            'post_id' => $post->id,
        ], 201);
    }
}
