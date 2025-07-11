<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Category;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * API認証テスト
     */
    public function test_api_authentication_required(): void
    {
        $response = $this->getJson('/api/categories');

        $response->assertStatus(401);
    }

    /**
     * APIでカテゴリ一覧を取得できることをテスト
     */
    public function test_api_can_get_categories(): void
    {
        $user = User::factory()->create();
        $categories = Category::factory()->count(3)->create(['user_id' => $user->id]);

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/categories');

        $response->assertStatus(200)
                 ->assertJsonCount(3, 'data')
                 ->assertJsonStructure([
                     'data' => [
                         '*' => [
                             'id',
                             'name',
                             'description',
                             'color',
                             'icon',
                             'created_at',
                             'updated_at',
                         ]
                     ]
                 ]);
    }

    /**
     * APIでカテゴリを作成できることをテスト
     */
    public function test_api_can_create_category(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/categories', [
            'name' => 'Test Category',
            'description' => 'Test description',
            'color' => '#FF5733',
            'icon' => '📝',
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'data' => [
                         'id',
                         'name',
                         'description',
                         'color',
                         'icon',
                         'created_at',
                         'updated_at',
                     ]
                 ]);

        $this->assertDatabaseHas('categories', [
            'name' => 'Test Category',
            'description' => 'Test description',
            'user_id' => $user->id,
        ]);
    }

    /**
     * APIでカテゴリ作成時のバリデーションテスト
     */
    public function test_api_category_creation_validation(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/categories', [
            'name' => '', // 必須フィールドが空
            'description' => 'Test description',
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['name']);
    }

    /**
     * APIでカテゴリの詳細を取得できることをテスト
     */
    public function test_api_can_get_category_details(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user, 'sanctum')->getJson("/api/categories/{$category->id}");

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                         'id',
                         'name',
                         'description',
                         'color',
                         'icon',
                         'created_at',
                         'updated_at',
                     ]
                 ]);
    }

    /**
     * APIで他のユーザーのカテゴリにアクセスできないことをテスト
     */
    public function test_api_cannot_access_other_users_category(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($user, 'sanctum')->getJson("/api/categories/{$category->id}");

        $response->assertStatus(404);
    }

    /**
     * APIでカテゴリを更新できることをテスト
     */
    public function test_api_can_update_category(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user, 'sanctum')->putJson("/api/categories/{$category->id}", [
            'name' => 'Updated Category',
            'description' => 'Updated description',
            'color' => '#00FF00',
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'data' => [
                         'id' => $category->id,
                         'name' => 'Updated Category',
                         'description' => 'Updated description',
                         'color' => '#00FF00',
                     ]
                 ]);

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Updated Category',
            'description' => 'Updated description',
        ]);
    }

    /**
     * APIでカテゴリを削除できることをテスト
     */
    public function test_api_can_delete_category(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user, 'sanctum')->deleteJson("/api/categories/{$category->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    /**
     * APIでタスク一覧を取得できることをテスト
     */
    public function test_api_can_get_tasks(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);
        $tasks = Task::factory()->count(3)->create(['category_id' => $category->id]);

        $response = $this->actingAs($user, 'sanctum')->getJson("/api/categories/{$category->id}/tasks");

        $response->assertStatus(200)
                 ->assertJsonCount(3, 'data')
                 ->assertJsonStructure([
                     'data' => [
                         '*' => [
                             'id',
                             'title',
                             'description',
                             'status',
                             'priority',
                             'due_date',
                             'created_at',
                             'updated_at',
                         ]
                     ]
                 ]);
    }

    /**
     * APIでタスクを作成できることをテスト
     */
    public function test_api_can_create_task(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user, 'sanctum')->postJson("/api/categories/{$category->id}/tasks", [
            'title' => 'Test Task',
            'description' => 'Test task description',
            'priority' => 'medium',
            'due_date' => '2024-12-31',
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'data' => [
                         'id',
                         'title',
                         'description',
                         'status',
                         'priority',
                         'due_date',
                         'created_at',
                         'updated_at',
                     ]
                 ]);

        $this->assertDatabaseHas('tasks', [
            'title' => 'Test Task',
            'description' => 'Test task description',
            'category_id' => $category->id,
        ]);
    }

    /**
     * APIでタスクのステータスを更新できることをテスト
     */
    public function test_api_can_update_task_status(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);
        $task = Task::factory()->create(['category_id' => $category->id, 'status' => 'pending']);

        $response = $this->actingAs($user, 'sanctum')->patchJson("/api/categories/{$category->id}/tasks/{$task->id}/status", [
            'status' => 'completed',
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'data' => [
                         'id' => $task->id,
                         'status' => 'completed',
                     ]
                 ]);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => 'completed',
        ]);
    }

    /**
     * APIでタスクを削除できることをテスト
     */
    public function test_api_can_delete_task(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);
        $task = Task::factory()->create(['category_id' => $category->id]);

        $response = $this->actingAs($user, 'sanctum')->deleteJson("/api/categories/{$category->id}/tasks/{$task->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    /**
     * APIでダッシュボードデータを取得できることをテスト
     */
    public function test_api_can_get_dashboard_data(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);
        Task::factory()->count(5)->create(['category_id' => $category->id, 'status' => 'pending']);
        Task::factory()->count(3)->create(['category_id' => $category->id, 'status' => 'completed']);

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/dashboard');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                         'total_categories',
                         'total_tasks',
                         'pending_tasks',
                         'completed_tasks',
                         'recent_tasks',
                     ]
                 ]);
    }

    /**
     * APIでユーザー統計情報を取得できることをテスト
     */
    public function test_api_can_get_user_statistics(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);
        Task::factory()->count(10)->create(['category_id' => $category->id]);

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/user/statistics');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                         'total_categories',
                         'total_tasks',
                         'completed_tasks',
                         'pending_tasks',
                         'completion_rate',
                     ]
                 ]);
    }

    /**
     * APIで検索機能をテスト
     */
    public function test_api_can_search_tasks(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);
        Task::factory()->create(['category_id' => $category->id, 'title' => 'Find this task']);
        Task::factory()->create(['category_id' => $category->id, 'title' => 'Another task']);

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/search/tasks?q=Find');

        $response->assertStatus(200)
                 ->assertJsonCount(1, 'data')
                 ->assertJsonPath('data.0.title', 'Find this task');
    }

    /**
     * APIでページネーションをテスト
     */
    public function test_api_pagination_works(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);
        Task::factory()->count(25)->create(['category_id' => $category->id]);

        $response = $this->actingAs($user, 'sanctum')->getJson("/api/categories/{$category->id}/tasks?page=1&per_page=10");

        $response->assertStatus(200)
                 ->assertJsonCount(10, 'data')
                 ->assertJsonStructure([
                     'data',
                     'links',
                     'meta' => [
                         'current_page',
                         'per_page',
                         'total',
                         'last_page',
                     ]
                 ]);
    }

    /**
     * APIでソート機能をテスト
     */
    public function test_api_can_sort_tasks(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);
        $task1 = Task::factory()->create(['category_id' => $category->id, 'title' => 'A Task']);
        $task2 = Task::factory()->create(['category_id' => $category->id, 'title' => 'B Task']);

        $response = $this->actingAs($user, 'sanctum')->getJson("/api/categories/{$category->id}/tasks?sort=title&order=asc");

        $response->assertStatus(200)
                 ->assertJsonPath('data.0.title', 'A Task')
                 ->assertJsonPath('data.1.title', 'B Task');
    }

    /**
     * APIで期限切れタスクを取得できることをテスト
     */
    public function test_api_can_get_overdue_tasks(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);
        Task::factory()->create([
            'category_id' => $category->id,
            'due_date' => date('Y-m-d', strtotime('-1 day')),
            'status' => 'pending',
        ]);
        Task::factory()->create([
            'category_id' => $category->id,
            'due_date' => date('Y-m-d', strtotime('+1 day')),
            'status' => 'pending',
        ]);

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/tasks/overdue');

        $response->assertStatus(200)
                 ->assertJsonCount(1, 'data');
    }

    /**
     * APIエラーハンドリングテスト
     */
    public function test_api_error_handling(): void
    {
        $user = User::factory()->create();

        // 存在しないカテゴリにアクセス
        $response = $this->actingAs($user, 'sanctum')->getJson('/api/categories/999');

        $response->assertStatus(404)
                 ->assertJson([
                     'message' => 'Category not found',
                 ]);
    }

    /**
     * APIレート制限テスト
     */
    public function test_api_rate_limiting(): void
    {
        $user = User::factory()->create();

        // 複数回リクエストを送信してレート制限をテスト
        for ($i = 0; $i < 65; $i++) {
            $response = $this->actingAs($user, 'sanctum')->getJson('/api/categories');
            
            if ($i < 60) {
                $response->assertStatus(200);
            } else {
                // レート制限に達した場合
                $response->assertStatus(429);
                break;
            }
        }
    }
}
