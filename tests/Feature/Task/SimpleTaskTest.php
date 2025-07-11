<?php

namespace Tests\Feature\Task;

use App\Models\User;
use App\Models\Category;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SimpleTaskTest extends TestCase
{
    use RefreshDatabase;

    /**
     * タスクを作成できることをテスト
     */
    public function test_task_can_be_created(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);
        $task = Task::factory()->create([
            'category_id' => $category->id,
            'title' => 'Test Task',
        ]);

        $this->assertDatabaseHas('tasks', [
            'title' => 'Test Task',
            'category_id' => $category->id,
        ]);
    }

    /**
     * タスクが削除できることをテスト
     */
    public function test_task_can_be_deleted(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);
        $task = Task::factory()->create(['category_id' => $category->id]);

        $taskId = $task->id;
        $task->delete();

        $this->assertDatabaseMissing('tasks', ['id' => $taskId]);
    }

    /**
     * タスクが更新できることをテスト
     */
    public function test_task_can_be_updated(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);
        $task = Task::factory()->create([
            'category_id' => $category->id,
            'title' => 'Original Title',
        ]);

        $task->update(['title' => 'Updated Title']);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Updated Title',
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

        $task->update(['status' => 'completed']);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'status' => 'completed',
        ]);
    }

    /**
     * タスクがカテゴリに属することをテスト
     */
    public function test_task_belongs_to_category(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);
        $task = Task::factory()->create(['category_id' => $category->id]);

        $this->assertEquals($category->id, $task->category->id);
    }
}
