<?php

namespace Tests\Feature\Category;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /**
     * カテゴリ一覧ページにアクセスできることをテスト
     */
    public function test_categories_index_page_can_be_rendered(): void
    {
        $response = $this->actingAs($this->user)->get('/categories');

        $response->assertStatus(200);
    }

    /**
     * カテゴリを作成できることをテスト
     */
    public function test_category_can_be_created(): void
    {
        $response = $this->actingAs($this->user)->post('/categories', [
            'name' => 'Test Category',
            'display_order' => 1,
        ]);

        $response->assertRedirect('/categories');
        $this->assertDatabaseHas('categories', [
            'name' => 'Test Category',
            'user_id' => $this->user->id,
            'display_order' => 1,
        ]);
    }

    /**
     * カテゴリ作成時にファイルアップロードできることをテスト
     */
    public function test_category_can_be_created_with_icon(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('category-icon.png');

        $response = $this->actingAs($this->user)->post('/categories', [
            'name' => 'Test Category',
            'icon' => $file,
            'display_order' => 1,
        ]);

        $response->assertRedirect('/categories');
        $this->assertDatabaseHas('categories', [
            'name' => 'Test Category',
            'user_id' => $this->user->id,
        ]);

        Storage::disk('public')->assertExists('icons/' . $file->hashName());
    }

    /**
     * カテゴリ名が必須であることをテスト
     */
    public function test_category_name_is_required(): void
    {
        $response = $this->actingAs($this->user)->post('/categories', [
            'display_order' => 1,
        ]);

        $response->assertSessionHasErrors(['name']);
    }

    /**
     * カテゴリ名が255文字以内であることをテスト
     */
    public function test_category_name_cannot_exceed_255_characters(): void
    {
        $longName = str_repeat('a', 256);

        $response = $this->actingAs($this->user)->post('/categories', [
            'name' => $longName,
            'display_order' => 1,
        ]);

        $response->assertSessionHasErrors(['name']);
    }

    /**
     * カテゴリ詳細ページにアクセスできることをテスト
     */
    public function test_category_show_page_can_be_rendered(): void
    {
        $category = Category::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)->get("/categories/{$category->id}");

        $response->assertStatus(200);
    }

    /**
     * 他のユーザーのカテゴリにアクセスできないことをテスト
     */
    public function test_user_cannot_access_other_users_category(): void
    {
        $otherUser = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($this->user)->get("/categories/{$category->id}");

        $response->assertStatus(403);
    }

    /**
     * カテゴリを更新できることをテスト
     */
    public function test_category_can_be_updated(): void
    {
        $category = Category::factory()->create([
            'user_id' => $this->user->id,
            'name' => 'Original Name',
        ]);

        $response = $this->actingAs($this->user)->patch("/categories/{$category->id}", [
            'name' => 'Updated Name',
            'display_order' => 2,
        ]);

        $response->assertRedirect('/categories');
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Updated Name',
            'display_order' => 2,
        ]);
    }

    /**
     * 他のユーザーのカテゴリを更新できないことをテスト
     */
    public function test_user_cannot_update_other_users_category(): void
    {
        $otherUser = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($this->user)->patch("/categories/{$category->id}", [
            'name' => 'Hacked Name',
        ]);

        $response->assertStatus(403);
    }

    /**
     * カテゴリを削除できることをテスト
     */
    public function test_category_can_be_deleted(): void
    {
        $category = Category::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)->delete("/categories/{$category->id}");

        $response->assertRedirect('/categories');
        $this->assertDatabaseMissing('categories', [
            'id' => $category->id,
        ]);
    }

    /**
     * 他のユーザーのカテゴリを削除できないことをテスト
     */
    public function test_user_cannot_delete_other_users_category(): void
    {
        $otherUser = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($this->user)->delete("/categories/{$category->id}");

        $response->assertStatus(403);
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
        ]);
    }

    /**
     * カテゴリの並び替えができることをテスト
     */
    public function test_categories_can_be_reordered(): void
    {
        $category1 = Category::factory()->create(['user_id' => $this->user->id, 'display_order' => 1]);
        $category2 = Category::factory()->create(['user_id' => $this->user->id, 'display_order' => 2]);

        $response = $this->actingAs($this->user)->post('/categories/reorder', [
            'orders' => [
                ['id' => $category1->id, 'order' => 2],
                ['id' => $category2->id, 'order' => 1],
            ]
        ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'ok']);

        $this->assertDatabaseHas('categories', [
            'id' => $category1->id,
            'display_order' => 2,
        ]);
        $this->assertDatabaseHas('categories', [
            'id' => $category2->id,
            'display_order' => 1,
        ]);
    }

    /**
     * 無効なアイコンファイルはアップロードできないことをテスト
     */
    public function test_invalid_icon_file_cannot_be_uploaded(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('document.pdf', 100);

        $response = $this->actingAs($this->user)->post('/categories', [
            'name' => 'Test Category',
            'icon' => $file,
            'display_order' => 1,
        ]);

        $response->assertSessionHasErrors(['icon']);
    }

    /**
     * 大きすぎるアイコンファイルはアップロードできないことをテスト
     */
    public function test_large_icon_file_cannot_be_uploaded(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('large-icon.png')->size(11000); // 11MB

        $response = $this->actingAs($this->user)->post('/categories', [
            'name' => 'Test Category',
            'icon' => $file,
            'display_order' => 1,
        ]);

        $response->assertSessionHasErrors(['icon']);
    }

    /**
     * 大量カテゴリのページネーション機能をテスト
     */
    public function test_category_pagination_with_large_dataset(): void
    {
        // 50個のカテゴリを作成
        Category::factory()->count(50)->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)->get('/categories?page=1');
        $response->assertStatus(200);

        $response = $this->actingAs($this->user)->get('/categories?page=2');
        $response->assertStatus(200);
    }

    /**
     * カテゴリ検索機能をテスト
     */
    public function test_category_search(): void
    {
        Category::factory()->create([
            'user_id' => $this->user->id,
            'name' => 'Work Projects'
        ]);
        
        Category::factory()->create([
            'user_id' => $this->user->id,
            'name' => 'Personal Tasks'
        ]);

        $response = $this->actingAs($this->user)->get('/categories?search=Work');
        $response->assertStatus(200);
    }

    /**
     * カテゴリのソート機能をテスト
     */
    public function test_category_sorting(): void
    {
        Category::factory()->count(5)->create(['user_id' => $this->user->id]);

        // 名前順
        $response = $this->actingAs($this->user)->get('/categories?sort=name&order=asc');
        $response->assertStatus(200);

        // 作成日順
        $response = $this->actingAs($this->user)->get('/categories?sort=created_at&order=desc');
        $response->assertStatus(200);

        // 表示順
        $response = $this->actingAs($this->user)->get('/categories?sort=display_order&order=asc');
        $response->assertStatus(200);
    }

    /**
     * カテゴリとタスクのリレーション効率性をテスト
     */
    public function test_categories_with_tasks_loaded_efficiently(): void
    {
        $categories = Category::factory()->count(10)->create(['user_id' => $this->user->id]);
        
        // 各カテゴリにタスクを作成
        foreach ($categories as $category) {
            \App\Models\Task::factory()->count(5)->create(['category_id' => $category->id]);
        }

        \DB::enableQueryLog();
        
        $response = $this->actingAs($this->user)->get('/categories');
        
        $queries = \DB::getQueryLog();
        
        // N+1問題がないことを確認（クエリ数が少ないこと）
        $this->assertLessThan(5, count($queries));
        
        \DB::disableQueryLog();
        
        $response->assertStatus(200);
    }

    /**
     * カテゴリAPIのパフォーマンステスト
     */
    public function test_category_api_performance(): void
    {
        Category::factory()->count(100)->create(['user_id' => $this->user->id]);

        $startTime = microtime(true);
        
        $response = $this->actingAs($this->user)->getJson('/api/categories');
        
        $endTime = microtime(true);
        $responseTime = $endTime - $startTime;

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data',
            'meta'
        ]);

        // レスポンス時間が1秒以内であることを確認
        $this->assertLessThan(1.0, $responseTime);
    }

    /**
     * カテゴリキャッシュ機能をテスト
     */
    public function test_category_caching(): void
    {
        Category::factory()->count(20)->create(['user_id' => $this->user->id]);

        // 初回リクエスト（キャッシュされる）
        $response1 = $this->actingAs($this->user)->get('/categories');
        $response1->assertStatus(200);

        // 2回目のリクエスト（キャッシュから取得）
        $startTime = microtime(true);
        $response2 = $this->actingAs($this->user)->get('/categories');
        $endTime = microtime(true);
        
        $response2->assertStatus(200);
        
        // キャッシュされたレスポンスは高速であることを確認
        $this->assertLessThan(0.1, $endTime - $startTime);
    }

    /**
     * カテゴリの統計情報取得のパフォーマンステスト
     */
    public function test_category_statistics_performance(): void
    {
        $categories = Category::factory()->count(10)->create(['user_id' => $this->user->id]);
        
        // 各カテゴリに大量のタスクを作成
        foreach ($categories as $category) {
            \App\Models\Task::factory()->count(20)->create(['category_id' => $category->id]);
        }

        $startTime = microtime(true);
        
        $response = $this->actingAs($this->user)->getJson('/api/categories/statistics');
        
        $endTime = microtime(true);
        $responseTime = $endTime - $startTime;

        $response->assertStatus(200);
        
        // 統計情報の取得が2秒以内であることを確認
        $this->assertLessThan(2.0, $responseTime);
    }

    /**
     * カテゴリの一括操作機能をテスト
     */
    public function test_bulk_category_operations(): void
    {
        $categories = Category::factory()->count(3)->create(['user_id' => $this->user->id]);
        $categoryIds = $categories->pluck('id')->toArray();

        // 一括削除
        $response = $this->actingAs($this->user)->delete('/categories/bulk', [
            'category_ids' => $categoryIds
        ]);

        $response->assertRedirect();
        
        foreach ($categoryIds as $categoryId) {
            $this->assertDatabaseMissing('categories', [
                'id' => $categoryId
            ]);
        }
    }
}
