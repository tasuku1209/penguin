<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_投稿一覧を取得できる(): void
    {
        Post::factory()->count(3)->create();

        $response = $this->get(route('posts.index'));

        $response->assertStatus(200);
    }

    public function test_ログインユーザーは投稿を作成できる(): void
    {
        $user = User::factory()->create();

        $tags = Tag::factory()->count(2)->create();

        $response = $this->actingAs($user)->post(route('posts.store'), [
            'title' => 'テスト投稿',
            'body' => 'テスト本文',
            'tags' => $tags->pluck('id')->toArray(),
        ]);

        $response->assertRedirect(route('posts.index'));

        $this->assertDatabaseHas('posts', [
            'title' => 'テスト投稿',
            'body' => 'テスト本文',
            'user_id' => $user->id,
        ]);

        $post = Post::first();

        $this->assertCount(2, $post->tags);
    }

    public function test_未ログインユーザーは投稿できない(): void
    {
        $response = $this->post(route('posts.store'), [
            'title' => 'テスト投稿',
            'body' => 'テスト本文',
        ]);

        $response->assertRedirect(route('login'));
    }

    public function test_title必須バリデーション(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('posts.store'), [
            'title' => '',
            'body' => 'テスト本文',
        ]);

        $response->assertSessionHasErrors('title');
    }

    public function test_body必須バリデーション(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('posts.store'), [
            'title' => 'テスト投稿',
            'body' => '',
        ]);

        $response->assertSessionHasErrors('body');
    }

    public function test_title最大文字数バリデーション(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('posts.store'), [
            'title' => str_repeat('a', 256),
            'body' => 'テスト本文',
        ]);

        $response->assertSessionHasErrors('title');
    }

    public function test_title最大文字数ちょうどなら投稿できる(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('posts.store'), [
            'title' => str_repeat('a', 255),
            'body' => 'テスト本文',
        ]);

        $response->assertRedirect(route('posts.index'));

        $this->assertDatabaseHas('posts', [
            'title' => str_repeat('a', 255),
        ]);
    }

    public function test_投稿詳細を取得できる(): void
    {
        $post = Post::factory()->create();

        $response = $this->get(route('posts.show', $post));

        $response->assertStatus(200);
    }

    public function test_投稿を更新できる(): void
    {
        $user = User::factory()->create();

        $post = Post::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->put(route('posts.update', $post), [
            'title' => '更新後タイトル',
            'body' => '更新後本文',
        ]);

        $response->assertRedirect(route('posts.show', $post));

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'title' => '更新後タイトル',
        ]);
    }

    public function test_他人の投稿は更新できない(): void
    {
        $user = User::factory()->create();

        $otherUser = User::factory()->create();

        $post = Post::factory()->create([
            'user_id' => $otherUser->id,
        ]);

        $response = $this->actingAs($user)->put(route('posts.update', $post), [
            'title' => '更新',
            'body' => '更新本文',
        ]);

        $response->assertForbidden();
    }

    public function test_投稿を削除できる(): void
    {
        $user = User::factory()->create();

        $post = Post::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->delete(route('posts.destroy', $post));

        $response->assertRedirect(route('posts.index'));

        $this->assertDatabaseMissing('posts', [
            'id' => $post->id,
        ]);
    }

    public function test_他人の投稿は削除できない(): void
    {
        $user = User::factory()->create();

        $otherUser = User::factory()->create();

        $post = Post::factory()->create([
            'user_id' => $otherUser->id,
        ]);

        $response = $this->actingAs($user)->delete(route('posts.destroy', $post));

        $response->assertForbidden();
    }
}