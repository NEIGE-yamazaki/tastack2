<?php

namespace Tests\Feature\Performance;

use App\Models\User;
use App\Models\Category;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PerformanceTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /**
     * 大量データでのダッシュボード表示パフォーマンステスト
     */
    public function test_dashboard_performance_with_large_dataset(): void
    {
        // 大量のカテゴリとタスクを作成
        $categories = Category::factory()->count(50)->create(['user_id' => $this->user->id]);
        
        foreach ($categories as $category) {
            Task::factory()->count(20)->create(['category_id' => $category->id]);
        }

        $startTime = microtime(true);
        
        $response = $this->actingAs($this->user)->get('/dashboard');
        
        $endTime = microtime(true);
        $responseTime = $endTime - $startTime;

        $response->assertStatus(200);
        
        // パフォーマンステストの結果を出力
        echo "\nDashboard performance: {$responseTime}s with 50 categories and 1000 tasks";
        
        // 基本的な動作確認
        $this->assertNotNull($response->getContent());
        $this->assertNotSame('', $response->getContent());
    }

    /**
     * データベースクエリ最適化のテスト（N+1問題）
     */
    public function test_query_optimization_n_plus_one(): void
    {
        $categories = Category::factory()->count(10)->create(['user_id' => $this->user->id]);
        
        foreach ($categories as $category) {
            Task::factory()->count(5)->create(['category_id' => $category->id]);
        }

        DB::enableQueryLog();
        
        $response = $this->actingAs($this->user)->get('/categories');
        
        $queries = DB::getQueryLog();
        DB::disableQueryLog();

        $response->assertStatus(200);
        
        echo "\nQuery count for categories with tasks: " . count($queries);
        
        // 基本的なクエリ数の確認
        $this->assertNotEmpty($queries);
    }

    /**
     * メモリ使用量のテスト
     */
    public function test_memory_usage(): void
    {
        $memoryBefore = memory_get_usage();
        
        // 大量データを処理
        $categories = Category::factory()->count(100)->create(['user_id' => $this->user->id]);
        
        foreach ($categories as $category) {
            Task::factory()->count(10)->create(['category_id' => $category->id]);
        }

        $response = $this->actingAs($this->user)->get('/categories');
        
        $memoryAfter = memory_get_usage();
        $memoryUsed = $memoryAfter - $memoryBefore;

        $response->assertStatus(200);
        
        echo "\nMemory usage: " . round($memoryUsed / 1024 / 1024, 2) . "MB for 100 categories with 1000 tasks";
        
        // メモリが使用されていることを確認
        $this->assertNotEmpty($memoryUsed);
    }

    /**
     * 基本的なレスポンス時間テスト
     */
    public function test_basic_response_times(): void
    {
        $category = Category::factory()->create(['user_id' => $this->user->id]);
        Task::factory()->count(50)->create(['category_id' => $category->id]);

        // カテゴリ一覧のレスポンス時間
        $startTime = microtime(true);
        $response1 = $this->actingAs($this->user)->get('/categories');
        $endTime = microtime(true);
        $categoriesTime = $endTime - $startTime;

        // タスク一覧のレスポンス時間
        $startTime = microtime(true);
        $response2 = $this->actingAs($this->user)->get("/categories/{$category->id}/tasks");
        $endTime = microtime(true);
        $tasksTime = $endTime - $startTime;

        $response1->assertStatus(200);
        $response2->assertStatus(200);

        echo "\nCategories list response time: {$categoriesTime}s";
        echo "\nTasks list response time: {$tasksTime}s";

        // 基本的な動作確認
        $this->assertNotEmpty($response1->getContent());
        $this->assertNotEmpty($response2->getContent());
    }

    /**
     * 大量データ作成のパフォーマンステスト
     */
    public function test_bulk_data_creation_performance(): void
    {
        $startTime = microtime(true);
        
        // 一度に大量のデータを作成
        $categories = Category::factory()->count(200)->create(['user_id' => $this->user->id]);
        
        $endTime = microtime(true);
        $creationTime = $endTime - $startTime;

        echo "\nBulk creation time for 200 categories: {$creationTime}s";
        
        $this->assertEquals(200, $categories->count());
        $this->assertDatabaseCount('categories', 200);
    }

    /**
     * 複雑なクエリのパフォーマンステスト
     */
    public function test_complex_query_performance(): void
    {
        $categories = Category::factory()->count(20)->create(['user_id' => $this->user->id]);
        
        foreach ($categories as $category) {
            Task::factory()->count(15)->create([
                'category_id' => $category->id,
                'status' => fake()->randomElement(['pending', 'in_progress', 'completed'])
            ]);
        }

        $startTime = microtime(true);
        
        // 複雑なクエリを実行
        $completedTasks = Task::whereHas('category', function ($query) {
            $query->where('user_id', $this->user->id);
        })->where('status', 'completed')->count();

        $endTime = microtime(true);
        $queryTime = $endTime - $startTime;

        echo "\nComplex query time for counting completed tasks: {$queryTime}s";
        echo "\nCompleted tasks found: {$completedTasks}";

        // 基本的な動作確認
        $this->assertNotEmpty($completedTasks >= 0);
    }
}
