<?php

namespace Tests\Unit\Models;

use App\Models\User;
use App\Models\Category;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * カテゴリがユーザーに属することをテスト
     */
    public function test_category_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);

        $this->assertEquals($user->id, $category->user->id);
    }

    /**
     * カテゴリがタスクを持つことをテスト
     */
    public function test_category_has_tasks(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);
        $task = Task::factory()->create(['category_id' => $category->id]);

        $this->assertTrue($category->tasks->contains($task));
    }

    /**
     * カテゴリの完了済みタスクをテスト
     */
    public function test_category_completed_tasks(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);
        $completedTask = Task::factory()->create([
            'category_id' => $category->id,
            'status' => 'completed'
        ]);
        $pendingTask = Task::factory()->create([
            'category_id' => $category->id,
            'status' => 'pending'
        ]);

        $completedTasks = $category->completedTasks;

        $this->assertTrue($completedTasks->contains($completedTask));
        $this->assertFalse($completedTasks->contains($pendingTask));
    }

    /**
     * カテゴリの保留中タスクをテスト
     */
    public function test_category_pending_tasks(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);
        $pendingTask = Task::factory()->create([
            'category_id' => $category->id,
            'status' => 'pending'
        ]);
        $completedTask = Task::factory()->create([
            'category_id' => $category->id,
            'status' => 'completed'
        ]);

        $pendingTasks = $category->pendingTasks;

        $this->assertTrue($pendingTasks->contains($pendingTask));
        $this->assertFalse($pendingTasks->contains($completedTask));
    }

    /**
     * カテゴリの期限切れタスクをテスト
     */
    public function test_category_overdue_tasks(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);
        $overdueTask = Task::factory()->create([
            'category_id' => $category->id,
            'due_date' => date('Y-m-d', strtotime('-1 day')),
            'status' => 'pending'
        ]);
        $normalTask = Task::factory()->create([
            'category_id' => $category->id,
            'due_date' => date('Y-m-d', strtotime('+1 day')),
            'status' => 'pending'
        ]);

        $overdueTasks = $category->overdueTasks;

        $this->assertTrue($overdueTasks->contains($overdueTask));
        $this->assertFalse($overdueTasks->contains($normalTask));
    }

    /**
     * カテゴリのタスク数をテスト
     */
    public function test_category_task_count(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);
        Task::factory()->count(5)->create(['category_id' => $category->id]);

        $this->assertEquals(5, $category->tasks->count());
    }

    /**
     * カテゴリの完了率をテスト
     */
    public function test_category_completion_rate(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);
        
        // 10個のタスクを作成、そのうち3個を完了済みに
        Task::factory()->count(7)->create([
            'category_id' => $category->id,
            'status' => 'pending'
        ]);
        Task::factory()->count(3)->create([
            'category_id' => $category->id,
            'status' => 'completed'
        ]);

        $completionRate = $category->completionRate;

        $this->assertEquals(30, $completionRate);
    }

    /**
     * カテゴリにタスクがない場合の完了率をテスト
     */
    public function test_category_completion_rate_with_no_tasks(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);

        $completionRate = $category->completionRate;

        $this->assertEquals(0, $completionRate);
    }

    /**
     * カテゴリの色の検証をテスト
     */
    public function test_category_color_validation(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create([
            'user_id' => $user->id,
            'color' => '#FF5733'
        ]);

        $this->assertTrue($category->isValidColor($category->color));
    }

    /**
     * カテゴリのアイコンの検証をテスト
     */
    public function test_category_icon_validation(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create([
            'user_id' => $user->id,
            'icon' => '📝'
        ]);

        $this->assertTrue($category->hasIcon());
    }

    /**
     * カテゴリが共有されているかをテスト
     */
    public function test_category_is_shared(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);

        // 共有されていない場合
        $this->assertFalse($category->isShared);

        // 共有レコードを作成
        $category->shares()->create([
            'share_group_id' => 1,
            'permission' => 'read'
        ]);

        // 共有されている場合
        $category->refresh();
        $this->assertTrue($category->isShared);
    }

    /**
     * カテゴリの最終更新日をテスト
     */
    public function test_category_last_activity(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);
        $task = Task::factory()->create(['category_id' => $category->id]);

        $lastActivity = $category->lastActivity;

        $this->assertNotNull($lastActivity);
    }

    /**
     * カテゴリの順序をテスト
     */
    public function test_category_sort_order(): void
    {
        $user = User::factory()->create();
        $category1 = Category::factory()->create([
            'user_id' => $user->id,
            'sort_order' => 1
        ]);
        $category2 = Category::factory()->create([
            'user_id' => $user->id,
            'sort_order' => 2
        ]);

        $this->assertTrue($category1->sort_order < $category2->sort_order);
    }

    /**
     * カテゴリの表示名をテスト
     */
    public function test_category_display_name(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create([
            'user_id' => $user->id,
            'name' => 'Test Category',
            'icon' => '📝'
        ]);

        $displayName = $category->displayName;

        $this->assertEquals('📝 Test Category', $displayName);
    }

    /**
     * カテゴリのアイコンがない場合の表示名をテスト
     */
    public function test_category_display_name_without_icon(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create([
            'user_id' => $user->id,
            'name' => 'Test Category',
            'icon' => null
        ]);

        $displayName = $category->displayName;

        $this->assertEquals('Test Category', $displayName);
    }

    /**
     * カテゴリのスタイルをテスト
     */
    public function test_category_style(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create([
            'user_id' => $user->id,
            'color' => '#FF5733'
        ]);

        $style = $category->style;

        $this->assertStringContainsString('background-color: #FF5733', $style);
    }

    /**
     * カテゴリのスラッグをテスト
     */
    public function test_category_slug(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create([
            'user_id' => $user->id,
            'name' => 'Test Category'
        ]);

        $slug = $category->slug;

        $this->assertEquals('test-category', $slug);
    }

    /**
     * カテゴリの統計情報をテスト
     */
    public function test_category_statistics(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);
        
        Task::factory()->count(5)->create([
            'category_id' => $category->id,
            'status' => 'pending'
        ]);
        Task::factory()->count(3)->create([
            'category_id' => $category->id,
            'status' => 'completed'
        ]);

        $statistics = $category->statistics;

        $this->assertEquals(8, $statistics['total_tasks']);
        $this->assertEquals(5, $statistics['pending_tasks']);
        $this->assertEquals(3, $statistics['completed_tasks']);
        $this->assertEquals(37.5, $statistics['completion_rate']);
    }

    /**
     * カテゴリの削除時にタスクも削除されることをテスト
     */
    public function test_category_deletion_deletes_tasks(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);
        $task = Task::factory()->create(['category_id' => $category->id]);

        $taskId = $task->id;
        $category->delete();

        $this->assertDatabaseMissing('tasks', ['id' => $taskId]);
    }
}
