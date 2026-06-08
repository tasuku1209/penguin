<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_会員登録画面を表示できる(): void
    {
        $response = $this->get(route('register'));

        $response->assertStatus(200);
    }

    public function test_ユーザー登録できる(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
        ]);

        $this->assertAuthenticated();
    }

    public function test_name必須バリデーション(): void
    {
        $response = $this->from(route('register'))
            ->post(route('register'), [
                'name' => '',
                'email' => 'test@example.com',
                'password' => 'password',
                'password_confirmation' => 'password',
            ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_email必須バリデーション(): void
    {
        $response = $this->from(route('register'))
            ->post(route('register'), [
                'name' => 'テスト',
                'email' => '',
                'password' => 'password',
                'password_confirmation' => 'password',
            ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_password必須バリデーション(): void
    {
        $response = $this->from(route('register'))
            ->post(route('register'), [
                'name' => 'テスト',
                'email' => 'test@example.com',
                'password' => '',
                'password_confirmation' => '',
            ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_password確認一致バリデーション(): void
    {
        $response = $this->from(route('register'))
            ->post(route('register'), [
                'name' => 'テスト',
                'email' => 'test@example.com',
                'password' => 'password',
                'password_confirmation' => 'wrong-password',
            ]);

        $response->assertSessionHasErrors('password');
    }

    public function test_同じメールアドレスでは登録できない(): void
    {
        User::factory()->create([
            'email' => 'test@example.com',
        ]);

        $response = $this->from(route('register'))
            ->post(route('register'), [
                'name' => 'テスト',
                'email' => 'test@example.com',
                'password' => 'password',
                'password_confirmation' => 'password',
            ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_email形式バリデーション(): void
    {
        $response = $this->from(route('register'))
            ->post(route('register'), [
                'name' => 'テスト',
                'email' => 'invalid-email',
                'password' => 'password',
                'password_confirmation' => 'password',
            ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_name最大文字数バリデーション(): void
    {
        $response = $this->from(route('register'))
            ->post(route('register'), [
                'name' => str_repeat('a', 256),
                'email' => 'test@example.com',
                'password' => 'password',
                'password_confirmation' => 'password',
            ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_password最小文字数バリデーション(): void
    {
        $response = $this->from(route('register'))
            ->post(route('register'), [
                'name' => 'テスト',
                'email' => 'test@example.com',
                'password' => '123',
                'password_confirmation' => '123',
            ]);

        $response->assertSessionHasErrors('password');
    }
}