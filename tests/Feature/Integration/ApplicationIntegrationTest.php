<?php

namespace Tests\Feature\Integration;

use Tests\TestCase;

class ApplicationIntegrationTest extends TestCase
{
    /**
     * アプリケーションの基本ページが正常に動作することをテスト
     */
    public function test_homepage_loads_successfully(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    /**
     * 認証ページが正常に動作することをテスト
     */
    public function test_auth_pages_load_successfully(): void
    {
        // ログインページ
        $response = $this->get('/login');
        $response->assertStatus(200);

        // 登録ページ
        $response = $this->get('/register');
        $response->assertStatus(200);
    }

    /**
     * 静的ページが正常に動作することをテスト
     */
    public function test_static_pages_load_successfully(): void
    {
        // 利用規約ページ
        $response = $this->get('/termsofservice');
        $response->assertStatus(200);

        // プライバシーポリシーページ
        $response = $this->get('/privacypolicy');
        $response->assertStatus(200);

        // 運営会社ページ
        $response = $this->get('/company');
        $response->assertStatus(200);
    }

    /**
     * デモページが正常に動作することをテスト
     */
    public function test_demo_page_loads_successfully(): void
    {
        $response = $this->get('/demo');
        $response->assertStatus(200);
    }

    /**
     * モバイルテストページが正常に動作することをテスト
     */
    public function test_mobile_test_page_loads_successfully(): void
    {
        $response = $this->get('/mobile-test');
        $response->assertStatus(200);
    }

    /**
     * 存在しないページが404を返すことをテスト
     */
    public function test_nonexistent_page_returns_404(): void
    {
        $response = $this->get('/nonexistent-page');
        $response->assertStatus(404);
    }

    /**
     * CSRF保護が有効になっていることをテスト
     */
    public function test_csrf_protection_is_enabled(): void
    {
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        // CSRFトークンなしでPOSTリクエストを送信すると419エラーが返される
        $response->assertStatus(419);
    }

    /**
     * レスポンスヘッダーのセキュリティ設定をテスト
     */
    public function test_security_headers_are_present(): void
    {
        $response = $this->get('/');

        // X-Frame-Optionsヘッダーが設定されていることを確認
        $response->assertHeader('X-Frame-Options');
        
        // X-Content-Type-Optionsヘッダーが設定されていることを確認
        $response->assertHeader('X-Content-Type-Options');
    }

    /**
     * JSONレスポンスが正しい形式であることをテスト
     */
    public function test_json_response_format(): void
    {
        $response = $this->getJson('/api/categories');
        
        // 認証されていない場合は401エラーが返される
        $response->assertStatus(401);
        $response->assertJson([
            'message' => 'Unauthenticated.'
        ]);
    }

    /**
     * ミドルウェアが正常に動作することをテスト
     */
    public function test_middleware_functionality(): void
    {
        // 認証が必要なページに未認証でアクセス
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');

        // プロフィールページに未認証でアクセス
        $response = $this->get('/profile');
        $response->assertRedirect('/login');
    }

    /**
     * 環境設定が正しく読み込まれることをテスト
     */
    public function test_environment_configuration(): void
    {
        // アプリケーション名が設定されていることを確認
        $this->assertNotEmpty(config('app.name'));

        // データベース接続が設定されていることを確認
        $this->assertNotEmpty(config('database.default'));

        // メール設定が存在することを確認
        $this->assertNotEmpty(config('mail.default'));
    }

    /**
     * キャッシュシステムが動作することをテスト
     */
    public function test_cache_functionality(): void
    {
        $key = 'test_cache_key';
        $value = 'test_cache_value';

        // キャッシュに値を保存
        cache([$key => $value], 60);

        // キャッシュから値を取得
        $cachedValue = cache($key);

        $this->assertEquals($value, $cachedValue);

        // キャッシュを削除
        cache()->forget($key);

        // キャッシュが削除されたことを確認
        $this->assertNull(cache($key));
    }

    /**
     * ログ機能が動作することをテスト
     */
    public function test_logging_functionality(): void
    {
        // ログにメッセージを記録
        logger('Test log message');

        // ログが記録されることを確認（例外が発生しないことを確認）
        $this->assertTrue(true);
    }

    /**
     * セッション機能が動作することをテスト
     */
    public function test_session_functionality(): void
    {
        $key = 'test_session_key';
        $value = 'test_session_value';

        // セッションに値を保存
        session([$key => $value]);

        // セッションから値を取得
        $sessionValue = session($key);

        $this->assertEquals($value, $sessionValue);

        // セッションから値を削除
        session()->forget($key);

        // セッションが削除されたことを確認
        $this->assertNull(session($key));
    }

    /**
     * バリデーション機能が動作することをテスト
     */
    public function test_validation_functionality(): void
    {
        $validator = validator(['email' => 'invalid-email'], [
            'email' => 'required|email'
        ]);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('email', $validator->errors()->toArray());
    }

    /**
     * ファイルストレージが動作することをテスト
     */
    public function test_file_storage_functionality(): void
    {
        $disk = 'local';
        $filename = 'test_file.txt';
        $content = 'Test file content';

        // ファイルを保存
        \Storage::disk($disk)->put($filename, $content);

        // ファイルが存在することを確認
        $this->assertTrue(\Storage::disk($disk)->exists($filename));

        // ファイル内容を確認
        $this->assertEquals($content, \Storage::disk($disk)->get($filename));

        // ファイルを削除
        \Storage::disk($disk)->delete($filename);

        // ファイルが削除されたことを確認
        $this->assertFalse(\Storage::disk($disk)->exists($filename));
    }
}
