<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdatePostRequest;
use App\Http\Requests\StorePostRequest;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->only(['create', 'store', 'edit', 'update', 'destroy']);
    }
    
    public function index(Request $request)
    {
        $posts = Post::with('user', 'tags')
            ->withCount(['comments', 'likes']);

        if ($request->filled('keyword')) {
            $posts->where('title', 'like', '%' . $request->keyword . '%')
                ->orWhere('body', 'like', '%' . $request->keyword . '%');
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

        return view('posts.index', compact('posts'));
    }

    public function create()
    {
        $tags = Tag::all();
        return view('posts.create', compact('tags'));
    }

    public function store(StorePostRequest $request)
    {
        $validated = $request->validated();
        $validated['user_id'] = auth()->id(); 
        $post = Post::create($validated);
        $post->tags()->attach($request->input('tags', []));
        return redirect()->route('posts.index');
    }

    public function show(Post $post)
    {
        $post->load([
            'user', 
            'tags',
            'comments.user',
        ])->loadCount('likes');
        return view('posts.show', compact('post'));
    }

    public function edit(Post $post)
    {
        $this->authorize('update', $post);
        $tags = Tag::all();
        return view('posts.edit', compact('post', 'tags'));
    }

    public function update(UpdatePostRequest $request, Post $post)
    {
        $this->authorize('update', $post);
        $post->update($request->validated());
        $post->tags()->sync($request->input('tags', []));
        return redirect()->route('posts.show', $post);
    }

    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);
        $post->tags()->detach();
        $post->delete();
        return redirect()->route('posts.index');
    }
}
