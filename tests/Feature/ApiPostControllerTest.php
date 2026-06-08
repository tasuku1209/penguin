<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiPostControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_投稿一覧を取得できる(): void
    {
        Post::factory()->count(3)->create();

        $response = $this->getJson('/api/posts');

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'body',
                        'created_at',
                        'user',
                        'tags',
                        'comments_count',
                        'likes_count',
                    ]
                ]
            ]);
    }

    public function test_投稿詳細を取得できる(): void
    {
        $post = Post::factory()->create();

        $response = $this->getJson("/api/posts/{$post->id}");

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'title',
                    'body',
                    'created_at',
                    'user',
                    'tags',
                    'comments_count',
                    'likes_count',
                ]
            ]);
    }

    public function test_認証ユーザーは投稿作成できる(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $tags = Tag::factory()->count(2)->create();

        $response = $this->postJson('/api/posts', [
            'title' => 'APIテスト投稿',
            'body' => 'APIテスト本文',
            'tags' => $tags->pluck('id')->toArray(),
        ]);

        $response
            ->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'post_id',
            ]);

        $this->assertDatabaseHas('posts', [
            'title' => 'APIテスト投稿',
            'body' => 'APIテスト本文',
            'user_id' => $user->id,
        ]);
    }

    public function test_未認証ユーザーは投稿作成できない(): void
    {
        $response = $this->postJson('/api/posts', [
            'title' => 'APIテスト投稿',
            'body' => 'APIテスト本文',
        ]);

        $response->assertStatus(401);
    }

    public function test_title必須バリデーション(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/posts', [
            'title' => '',
            'body' => '本文',
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors('title');
    }

    public function test_body必須バリデーション(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/posts', [
            'title' => 'タイトル',
            'body' => '',
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors('body');
    }

    public function test_キーワード検索できる(): void
    {
        Post::factory()->create([
            'title' => 'Laravel投稿',
        ]);

        Post::factory()->create([
            'title' => 'PHP投稿',
        ]);

        $response = $this->getJson('/api/posts?keyword=Laravel');

        $response
            ->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    public function test_タグ検索できる(): void
    {
        $tag1 = Tag::factory()->create();
        $tag2 = Tag::factory()->create();

        $post1 = Post::factory()->create();
        $post2 = Post::factory()->create();

        $post1->tags()->attach($tag1);
        $post2->tags()->attach($tag2);

        $response = $this->getJson("/api/posts?tags[]={$tag1->id}");

        $response
            ->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    public function test_新着順ソートできる(): void
    {
        $oldPost = Post::factory()->create([
            'created_at' => now()->subDay(),
        ]);

        $newPost = Post::factory()->create([
            'created_at' => now(),
        ]);

        $response = $this->getJson('/api/posts?sort=latest');

        $response
            ->assertStatus(200)
            ->assertJsonPath('data.0.id', $newPost->id);
    }

    public function test_古い順ソートできる(): void
    {
        $oldPost = Post::factory()->create([
            'created_at' => now()->subDay(),
        ]);

        $newPost = Post::factory()->create([
            'created_at' => now(),
        ]);

        $response = $this->getJson('/api/posts?sort=oldest');

        $response
            ->assertStatus(200)
            ->assertJsonPath('data.0.id', $oldPost->id);
    }
}