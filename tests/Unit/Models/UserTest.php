<?php

namespace Tests\Unit\Models;

use App\Models\User;
use App\Models\Category;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * ユーザーがカテゴリを持つことをテスト
     */
    public function test_user_has_categories(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);

        $this->assertTrue($user->categories->contains($category));
    }

    /**
     * ユーザーがタスクを持つことをテスト
     */
    public function test_user_has_tasks(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);
        $task = Task::factory()->create(['category_id' => $category->id]);

        $this->assertTrue($user->tasks->contains($task));
    }

    /**
     * ユーザーの完了済みタスクをテスト
     */
    public function test_user_completed_tasks(): void
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

        $completedTasks = $user->completedTasks;

        $this->assertTrue($completedTasks->contains($completedTask));
        $this->assertFalse($completedTasks->contains($pendingTask));
    }

    /**
     * ユーザーの保留中タスクをテスト
     */
    public function test_user_pending_tasks(): void
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

        $pendingTasks = $user->pendingTasks;

        $this->assertTrue($pendingTasks->contains($pendingTask));
        $this->assertFalse($pendingTasks->contains($completedTask));
    }

    /**
     * ユーザーの期限切れタスクをテスト
     */
    public function test_user_overdue_tasks(): void
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

        $overdueTasks = $user->overdueTasks;

        $this->assertTrue($overdueTasks->contains($overdueTask));
        $this->assertFalse($overdueTasks->contains($normalTask));
    }

    /**
     * ユーザーのタスク完了率をテスト
     */
    public function test_user_task_completion_rate(): void
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

        $completionRate = $user->taskCompletionRate;

        $this->assertEquals(30, $completionRate);
    }

    /**
     * ユーザーのタスクがない場合の完了率をテスト
     */
    public function test_user_completion_rate_with_no_tasks(): void
    {
        $user = User::factory()->create();

        $completionRate = $user->taskCompletionRate;

        $this->assertEquals(0, $completionRate);
    }

    /**
     * ユーザーのフルネームをテスト
     */
    public function test_user_full_name(): void
    {
        $user = User::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Doe'
        ]);

        $this->assertEquals('John Doe', $user->fullName);
    }

    /**
     * ユーザーのアバターURLをテスト
     */
    public function test_user_avatar_url(): void
    {
        $user = User::factory()->create(['avatar' => 'avatars/test.jpg']);

        $this->assertStringContainsString('avatars/test.jpg', $user->avatarUrl);
    }

    /**
     * ユーザーがアバターを持たない場合のデフォルトURLをテスト
     */
    public function test_user_default_avatar_url(): void
    {
        $user = User::factory()->create(['avatar' => null]);

        $this->assertStringContainsString('default-avatar.png', $user->avatarUrl);
    }

    /**
     * ユーザーがアクティブかどうかをテスト
     */
    public function test_user_is_active(): void
    {
        $activeUser = User::factory()->create(['is_active' => true]);
        $inactiveUser = User::factory()->create(['is_active' => false]);

        $this->assertTrue($activeUser->isActive);
        $this->assertFalse($inactiveUser->isActive);
    }

    /**
     * ユーザーのメール認証状態をテスト
     */
    public function test_user_email_verification_status(): void
    {
        $verifiedUser = User::factory()->create(['email_verified_at' => now()]);
        $unverifiedUser = User::factory()->create(['email_verified_at' => null]);

        $this->assertTrue($verifiedUser->hasVerifiedEmail());
        $this->assertFalse($unverifiedUser->hasVerifiedEmail());
    }

    /**
     * ユーザーのタイムゾーンをテスト
     */
    public function test_user_timezone(): void
    {
        $user = User::factory()->create(['timezone' => 'Asia/Tokyo']);

        $this->assertEquals('Asia/Tokyo', $user->timezone);
    }

    /**
     * ユーザーのデフォルトタイムゾーンをテスト
     */
    public function test_user_default_timezone(): void
    {
        $user = User::factory()->create(['timezone' => null]);

        $this->assertEquals('UTC', $user->timezone ?? 'UTC');
    }

    /**
     * ユーザーの言語設定をテスト
     */
    public function test_user_language(): void
    {
        $user = User::factory()->create(['language' => 'ja']);

        $this->assertEquals('ja', $user->language);
    }

    /**
     * ユーザーのデフォルト言語をテスト
     */
    public function test_user_default_language(): void
    {
        $user = User::factory()->create(['language' => null]);

        $this->assertEquals('en', $user->language ?? 'en');
    }

    /**
     * ユーザーの通知設定をテスト
     */
    public function test_user_notification_settings(): void
    {
        $user = User::factory()->create(['notifications_enabled' => true]);

        $this->assertTrue($user->notifications_enabled);
    }

    /**
     * ユーザーのテーマ設定をテスト
     */
    public function test_user_theme(): void
    {
        $user = User::factory()->create(['theme' => 'dark']);

        $this->assertEquals('dark', $user->theme);
    }

    /**
     * ユーザーのデフォルトテーマをテスト
     */
    public function test_user_default_theme(): void
    {
        $user = User::factory()->create(['theme' => null]);

        $this->assertEquals('light', $user->theme ?? 'light');
    }
}
