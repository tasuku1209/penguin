<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UnauthenticatedRedirectTest extends TestCase
{
    use RefreshDatabase;

    public function test_未ログインユーザーは投稿作成画面へアクセスできない(): void
    {
        $response = $this->get(route('posts.create'));

        $response->assertRedirect(route('login'));
    }

    public function test_未ログインユーザーは投稿作成できない(): void
    {
        $response = $this->post(route('posts.store'), [
            'title' => 'テスト投稿',
            'body' => 'テスト本文',
        ]);

        $response->assertRedirect(route('login'));
    }

    public function test_未ログインユーザーは投稿編集画面へアクセスできない(): void
    {
        $post = Post::factory()->create();

        $response = $this->get(route('posts.edit', $post));

        $response->assertRedirect(route('login'));
    }

    public function test_未ログインユーザーは投稿更新できない(): void
    {
        $post = Post::factory()->create();

        $response = $this->put(route('posts.update', $post), [
            'title' => '更新',
            'body' => '更新本文',
        ]);

        $response->assertRedirect(route('login'));
    }

    public function test_未ログインユーザーは投稿削除できない(): void
    {
        $post = Post::factory()->create();

        $response = $this->delete(route('posts.destroy', $post));

        $response->assertRedirect(route('login'));
    }

    public function test_未ログインユーザーはコメント投稿できない(): void
    {
        $post = Post::factory()->create();

        $response = $this->post(route('comments.store', $post), [
            'body' => 'コメント',
        ]);

        $response->assertRedirect(route('login'));
    }

    public function test_未ログインユーザーはコメント削除できない(): void
    {
        $post = Post::factory()->create();

        $comment = Comment::factory()->create([
            'post_id' => $post->id,
        ]);

        $response = $this->delete(
            route('comments.destroy', [$post, $comment])
        );

        $response->assertRedirect(route('login'));
    }

    public function test_未ログインユーザーはいいねできない(): void
    {
        $post = Post::factory()->create();

        $response = $this->post(route('likes.store', $post));

        $response->assertRedirect(route('login'));
    }
}