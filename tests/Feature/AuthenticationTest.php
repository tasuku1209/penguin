<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_ログイン画面を表示できる(): void
    {
        $response = $this->get(route('login'));

        $response->assertStatus(200);
    }

    public function test_ユーザーはログインできる(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
        ]);

        $response = $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect();

        $this->assertAuthenticatedAs($user);
    }

    public function test_パスワードが違うとログインできない(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
        ]);

        $response = $this->from(route('login'))->post(route('login'), [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $response->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function test_email必須バリデーション(): void
    {
        $response = $this->from(route('login'))->post(route('login'), [
            'email' => '',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_password必須バリデーション(): void
    {
        $response = $this->from(route('login'))->post(route('login'), [
            'email' => 'test@example.com',
            'password' => '',
        ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_ログインユーザーはログアウトできる(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('logout'));

        $response->assertRedirect('/');

        $this->assertGuest();
    }

    public function test_ゲストユーザーは保護ページへアクセスできない(): void
    {
        $post = \App\Models\Post::factory()->create();

        $response = $this->get(route('posts.create'));

        $response->assertRedirect(route('login'));
    }

    public function test_ログインユーザーは投稿作成画面へアクセスできる(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get(route('posts.create'));

        $response->assertStatus(200);
    }
}