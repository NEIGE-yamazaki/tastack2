<?php

namespace Tests\Feature\Task;

use App\Models\User;
use App\Models\Category;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    /**
     * タスク一覧画面にアクセスできることをテスト
     */
    public function test_tasks_index_page_can_be_rendered(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get("/categories/{$category->id}/tasks");

        $response->assertStatus(200);
    }

    /**
     * タスクを作成できることをテスト
     */
    public function test_task_can_be_created(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->post("/categories/{$category->id}/tasks", [
            'title' => 'Test Task',
            'description' => 'Test task description',
            'due_date' => '2024-12-31',
            'priority' => 'medium',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('tasks', [
            'title' => 'Test Task',
            'description' => 'Test task description',
            'category_id' => $category->id,
            'priority' => 'medium',
        ]);
    }

    /**
     * タスクタイトルが必須であることをテスト
     */
    public function test_task_title_is_required(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->post("/categories/{$category->id}/tasks", [
            'title' => '',
            'description' => 'Test task description',
        ]);

        $response->assertSessionHasErrors(['title']);
    }

    /**
     * タスクタイトルが255文字を超えないことをテスト
     */
    public function test_task_title_cannot_exceed_255_characters(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->post("/categories/{$category->id}/tasks", [
            'title' => str_repeat('a', 256),
            'description' => 'Test task description',
        ]);

        $response->assertSessionHasErrors(['title']);
    }

    /**
     * タスクの詳細画面にアクセスできることをテスト
     */
    public function test_task_show_page_can_be_rendered(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);
        $task = Task::factory()->create([
            'category_id' => $category->id,
            'title' => 'Test Task',
        ]);

        $response = $this->actingAs($user)->get("/categories/{$category->id}/tasks/{$task->id}");

        $response->assertStatus(200);
    }

    /**
     * 他のユーザーのタスクにアクセスできないことをテスト
     */
    public function test_user_cannot_access_other_users_task(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $otherUser->id]);
        $task = Task::factory()->create([
            'category_id' => $category->id,
            'title' => 'Other User Task',
        ]);

        $response = $this->actingAs($user)->get("/categories/{$category->id}/tasks/{$task->id}");

        $response->assertStatus(404);
    }

    /**
     * タスクを更新できることをテスト
     */
    public function test_task_can_be_updated(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);
        $task = Task::factory()->create([
            'category_id' => $category->id,
            'title' => 'Original Title',
        ]);

        $response = $this->actingAs($user)->put("/categories/{$category->id}/tasks/{$task->id}", [
            'title' => 'Updated Title',
            'description' => 'Updated description',
            'priority' => 'high',
            'status' => 'completed',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Updated Title',
            'description' => 'Updated description',
            'priority' => 'high',
            'status' => 'completed',
        ]);
    }

    /**
     * 他のユーザーのタスクを更新できないことをテスト
     */
    public function test_user_cannot_update_other_users_task(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $otherUser->id]);
        $task = Task::factory()->create([
            'category_id' => $category->id,
            'title' => 'Other User Task',
        ]);

        $response = $this->actingAs($user)->put("/categories/{$category->id}/tasks/{$task->id}", [
            'title' => 'Updated Title',
        ]);

        $response->assertStatus(404);
    }

    /**
     * タスクを削除できることをテスト
     */
    public function test_task_can_be_deleted(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);
        $task = Task::factory()->create([
            'category_id' => $category->id,
            'title' => 'Task to Delete',
        ]);

        $response = $this->actingAs($user)->delete("/categories/{$category->id}/tasks/{$task->id}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('tasks', [
            'id' => $task->id,
        ]);
    }

    /**
     * 他のユーザーのタスクを削除できないことをテスト
     */
    public function test_user_cannot_delete_other_users_task(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $otherUser->id]);
        $task = Task::factory()->create([
            'category_id' => $category->id,
            'title' => 'Other User Task',
        ]);

        $response = $this->actingAs($user)->delete("/categories/{$category->id}/tasks/{$task->id}");

        $response->assertStatus(404);
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
        ]);
    }

    /**
     * タスクのステータスを変更できることをテスト
     */
    public function test_task_status_can_be_changed(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);
        $task = Task::factory()->create([
            'category_id' => $category->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($user)->patch("/categories/{$category->id}/tasks/{$task->id}/status", [
            'status' => 'completed',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => 'completed',
        ]);
    }

    /**
     * 無効なステータスでタスクを更新できないことをテスト
     */
    public function test_invalid_status_cannot_be_set(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);
        $task = Task::factory()->create([
            'category_id' => $category->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($user)->patch("/categories/{$category->id}/tasks/{$task->id}/status", [
            'status' => 'invalid_status',
        ]);

        $response->assertSessionHasErrors(['status']);
    }

    /**
     * タスクの優先度を変更できることをテスト
     */
    public function test_task_priority_can_be_changed(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);
        $task = Task::factory()->create([
            'category_id' => $category->id,
            'priority' => 'low',
        ]);

        $response = $this->actingAs($user)->put("/categories/{$category->id}/tasks/{$task->id}", [
            'title' => $task->title,
            'priority' => 'high',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'priority' => 'high',
        ]);
    }

    /**
     * 期限切れのタスクを取得できることをテスト
     */
    public function test_can_retrieve_overdue_tasks(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);
        
        // 期限切れのタスクを作成
        $overdueTask = Task::factory()->create([
            'category_id' => $category->id,
            'due_date' => date('Y-m-d', strtotime('-1 day')),
            'status' => 'pending',
        ]);
        
        // 期限内のタスクを作成
        $normalTask = Task::factory()->create([
            'category_id' => $category->id,
            'due_date' => date('Y-m-d', strtotime('+1 day')),
            'status' => 'pending',
        ]);

        $response = $this->actingAs($user)->get("/categories/{$category->id}/tasks?filter=overdue");

        $response->assertStatus(200);
    }

    /**
     * 大量タスクのページネーション機能をテスト
     */
    public function test_task_pagination_with_large_dataset(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);
        
        // 30個のタスクを作成（15個/ページでページネーション）
        Task::factory()->count(30)->create(['category_id' => $category->id]);

        $response = $this->actingAs($user)->get("/categories/{$category->id}/tasks?page=1");
        $response->assertStatus(200);

        $response = $this->actingAs($user)->get("/categories/{$category->id}/tasks?page=2");
        $response->assertStatus(200);
    }

    /**
     * タスク検索・フィルタリング機能をテスト
     */
    public function test_task_search_and_filtering(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);
        
        // 様々なタスクを作成
        Task::factory()->create([
            'category_id' => $category->id,
            'title' => 'Important Meeting',
            'priority' => 'high',
            'status' => 'pending'
        ]);
        
        Task::factory()->create([
            'category_id' => $category->id,
            'title' => 'Review Code',
            'priority' => 'medium',
            'status' => 'completed'
        ]);

        // タイトル検索
        $response = $this->actingAs($user)->get("/categories/{$category->id}/tasks?search=Meeting");
        $response->assertStatus(200);

        // 優先度フィルター
        $response = $this->actingAs($user)->get("/categories/{$category->id}/tasks?priority=high");
        $response->assertStatus(200);

        // ステータスフィルター
        $response = $this->actingAs($user)->get("/categories/{$category->id}/tasks?status=completed");
        $response->assertStatus(200);
    }

    /**
     * タスクのソート機能をテスト
     */
    public function test_task_sorting(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);
        
        Task::factory()->count(5)->create(['category_id' => $category->id]);

        // 作成日順
        $response = $this->actingAs($user)->get("/categories/{$category->id}/tasks?sort=created_at&order=desc");
        $response->assertStatus(200);

        // 優先度順
        $response = $this->actingAs($user)->get("/categories/{$category->id}/tasks?sort=priority&order=asc");
        $response->assertStatus(200);

        // 期限順
        $response = $this->actingAs($user)->get("/categories/{$category->id}/tasks?sort=due_date&order=asc");
        $response->assertStatus(200);
    }

    /**
     * タスクの一括操作機能をテスト
     */
    public function test_bulk_task_operations(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);
        
        $tasks = Task::factory()->count(3)->create([
            'category_id' => $category->id,
            'status' => 'pending'
        ]);

        $taskIds = $tasks->pluck('id')->toArray();

        // 一括ステータス更新
        $response = $this->actingAs($user)->patch("/categories/{$category->id}/tasks/bulk", [
            'task_ids' => $taskIds,
            'action' => 'complete'
        ]);

        $response->assertRedirect();
        
        foreach ($taskIds as $taskId) {
            $this->assertDatabaseHas('tasks', [
                'id' => $taskId,
                'status' => 'completed'
            ]);
        }
    }

    /**
     * N+1クエリ問題の解決をテスト（with使用）
     */
    public function test_tasks_with_categories_loaded_efficiently(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);
        
        Task::factory()->count(10)->create(['category_id' => $category->id]);

        // SQLクエリ数を監視してN+1問題がないことを確認
        \DB::enableQueryLog();
        
        $response = $this->actingAs($user)->get("/categories/{$category->id}/tasks");
        
        $queries = \DB::getQueryLog();
        
        // カテゴリとタスクのクエリが効率的に実行されることを確認
        $this->assertLessThan(5, count($queries));
        
        \DB::disableQueryLog();
        
        $response->assertStatus(200);
    }

    /**
     * タスクのAPIエンドポイントのパフォーマンステスト
     */
    public function test_task_api_performance(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);
        
        Task::factory()->count(100)->create(['category_id' => $category->id]);

        $startTime = microtime(true);
        
        $response = $this->actingAs($user)->getJson("/api/categories/{$category->id}/tasks");
        
        $endTime = microtime(true);
        $responseTime = $endTime - $startTime;

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data',
            'meta' => [
                'current_page',
                'total',
                'per_page'
            ]
        ]);

        // レスポンス時間が1秒以内であることを確認
        $this->assertLessThan(1.0, $responseTime);
    }

    /**
     * タスクキャッシュ機能をテスト
     */
    public function test_task_caching(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);
        
        Task::factory()->count(10)->create(['category_id' => $category->id]);

        // 初回リクエスト（キャッシュされる）
        $response1 = $this->actingAs($user)->get("/categories/{$category->id}/tasks");
        $response1->assertStatus(200);

        // 2回目のリクエスト（キャッシュから取得）
        $startTime = microtime(true);
        $response2 = $this->actingAs($user)->get("/categories/{$category->id}/tasks");
        $endTime = microtime(true);
        
        $response2->assertStatus(200);
        
        // キャッシュされたレスポンスは高速であることを確認
        $this->assertLessThan(0.1, $endTime - $startTime);
    }
}
