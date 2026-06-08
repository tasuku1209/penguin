<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_ログインユーザーはコメント投稿できる(): void
    {
        $user = User::factory()->create();

        $post = Post::factory()->create();

        $response = $this->actingAs($user)->post(
            route('comments.store', $post),
            [
                'body' => 'テストコメント',
            ]
        );

        $response->assertRedirect(route('posts.show', $post));

        $this->assertDatabaseHas('comments', [
            'body' => 'テストコメント',
            'user_id' => $user->id,
            'post_id' => $post->id,
        ]);
    }

    public function test_未ログインユーザーはコメント投稿できない(): void
    {
        $post = Post::factory()->create();

        $response = $this->post(
            route('comments.store', $post),
            [
                'body' => 'テストコメント',
            ]
        );

        $response->assertRedirect(route('login'));
    }

    public function test_body必須バリデーション(): void
    {
        $user = User::factory()->create();

        $post = Post::factory()->create();

        $response = $this->actingAs($user)->post(
            route('comments.store', $post),
            [
                'body' => '',
            ]
        );

        $response->assertSessionHasErrors('body');
    }

    public function test_コメントを削除できる(): void
    {
        $user = User::factory()->create();

        $post = Post::factory()->create();

        $comment = Comment::factory()->create([
            'user_id' => $user->id,
            'post_id' => $post->id,
        ]);

        $response = $this->actingAs($user)->delete(
            route('comments.destroy', [$post, $comment])
        );

        $response->assertRedirect(route('posts.show', $post));

        $this->assertDatabaseMissing('comments', [
            'id' => $comment->id,
        ]);
    }

    public function test_他人のコメントは削除できない(): void
    {
        $user = User::factory()->create();

        $otherUser = User::factory()->create();

        $post = Post::factory()->create();

        $comment = Comment::factory()->create([
            'user_id' => $otherUser->id,
            'post_id' => $post->id,
        ]);

        $response = $this->actingAs($user)->delete(
            route('comments.destroy', [$post, $comment])
        );

        $response->assertForbidden();
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
}