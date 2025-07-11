<?php

namespace Tests\Feature\Profile;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    /**
     * プロフィール画面にアクセスできることをテスト
     */
    public function test_profile_page_can_be_rendered(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/profile');

        $response->assertStatus(200);
    }

    /**
     * プロフィール情報を更新できることをテスト
     */
    public function test_profile_information_can_be_updated(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->patch('/profile', [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
        ]);
    }

    /**
     * プロフィール更新時に名前が必須であることをテスト
     */
    public function test_profile_update_requires_name(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->patch('/profile', [
            'name' => '',
            'email' => 'updated@example.com',
        ]);

        $response->assertSessionHasErrors(['name']);
    }

    /**
     * プロフィール更新時に有効なメールアドレスが必要であることをテスト
     */
    public function test_profile_update_requires_valid_email(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->patch('/profile', [
            'name' => 'Updated Name',
            'email' => 'invalid-email',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    /**
     * メールアドレスが一意であることをテスト
     */
    public function test_profile_update_requires_unique_email(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create(['email' => 'other@example.com']);

        $response = $this->actingAs($user)->patch('/profile', [
            'name' => 'Updated Name',
            'email' => 'other@example.com',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    /**
     * パスワードを更新できることをテスト
     */
    public function test_password_can_be_updated(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->put('/password', [
            'current_password' => 'password',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response->assertRedirect();
    }

    /**
     * パスワード更新時に現在のパスワードが必要であることをテスト
     */
    public function test_password_update_requires_current_password(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->put('/password', [
            'current_password' => 'wrong-password',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response->assertSessionHasErrors(['current_password']);
    }

    /**
     * パスワード更新時に確認が必要であることをテスト
     */
    public function test_password_update_requires_confirmation(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->put('/password', [
            'current_password' => 'password',
            'password' => 'new-password',
            'password_confirmation' => 'different-password',
        ]);

        $response->assertSessionHasErrors(['password']);
    }

    /**
     * プロフィール画像をアップロードできることをテスト
     */
    public function test_profile_image_can_be_uploaded(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();

        $file = UploadedFile::fake()->image('avatar.jpg');

        $response = $this->actingAs($user)->post('/profile/avatar', [
            'avatar' => $file,
        ]);

        $response->assertRedirect();
        Storage::disk('public')->assertExists('avatars/' . $file->hashName());
    }

    /**
     * プロフィール画像のファイルタイプが制限されることをテスト
     */
    public function test_profile_image_upload_validates_file_type(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();

        $file = UploadedFile::fake()->create('document.pdf', 1024, 'application/pdf');

        $response = $this->actingAs($user)->post('/profile/avatar', [
            'avatar' => $file,
        ]);

        $response->assertSessionHasErrors(['avatar']);
    }

    /**
     * プロフィール画像のファイルサイズが制限されることをテスト
     */
    public function test_profile_image_upload_validates_file_size(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();

        // 5MBを超えるファイルを作成
        $file = UploadedFile::fake()->create('large-image.jpg', 5120, 'image/jpeg');

        $response = $this->actingAs($user)->post('/profile/avatar', [
            'avatar' => $file,
        ]);

        $response->assertSessionHasErrors(['avatar']);
    }

    /**
     * プロフィール画像を削除できることをテスト
     */
    public function test_profile_image_can_be_deleted(): void
    {
        Storage::fake('public');
        $user = User::factory()->create(['avatar' => 'avatars/test-avatar.jpg']);

        $response = $this->actingAs($user)->delete('/profile/avatar');

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'avatar' => null,
        ]);
    }

    /**
     * アカウントを削除できることをテスト
     */
    public function test_account_can_be_deleted(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->delete('/profile', [
            'password' => 'password',
        ]);

        $response->assertRedirect('/');
        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
    }

    /**
     * アカウント削除時にパスワードが必要であることをテスト
     */
    public function test_account_deletion_requires_password(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->delete('/profile', [
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors(['password']);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
        ]);
    }

    /**
     * ユーザー設定を更新できることをテスト
     */
    public function test_user_settings_can_be_updated(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->patch('/profile/settings', [
            'language' => 'ja',
            'timezone' => 'Asia/Tokyo',
            'notifications_enabled' => true,
            'theme' => 'dark',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'language' => 'ja',
            'timezone' => 'Asia/Tokyo',
            'notifications_enabled' => true,
            'theme' => 'dark',
        ]);
    }

    /**
     * 無効なタイムゾーンが拒否されることをテスト
     */
    public function test_invalid_timezone_is_rejected(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->patch('/profile/settings', [
            'timezone' => 'Invalid/Timezone',
        ]);

        $response->assertSessionHasErrors(['timezone']);
    }

    /**
     * 無効な言語が拒否されることをテスト
     */
    public function test_invalid_language_is_rejected(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->patch('/profile/settings', [
            'language' => 'invalid_language',
        ]);

        $response->assertSessionHasErrors(['language']);
    }

    /**
     * 無効なテーマが拒否されることをテスト
     */
    public function test_invalid_theme_is_rejected(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->patch('/profile/settings', [
            'theme' => 'invalid_theme',
        ]);

        $response->assertSessionHasErrors(['theme']);
    }
}
