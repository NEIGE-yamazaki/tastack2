<?php

namespace Tests\Feature\Category;

use App\Models\User;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SimpleCategoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * カテゴリを作成できることをテスト
     */
    public function test_category_can_be_created(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create([
            'user_id' => $user->id,
            'name' => 'Test Category',
        ]);

        $this->assertDatabaseHas('categories', [
            'name' => 'Test Category',
            'user_id' => $user->id,
        ]);
    }

    /**
     * ユーザーが複数のカテゴリを持つことができることをテスト
     */
    public function test_user_can_have_multiple_categories(): void
    {
        $user = User::factory()->create();
        $category1 = Category::factory()->create(['user_id' => $user->id]);
        $category2 = Category::factory()->create(['user_id' => $user->id]);

        $this->assertCount(2, $user->categories);
    }

    /**
     * カテゴリが削除できることをテスト
     */
    public function test_category_can_be_deleted(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);

        $categoryId = $category->id;
        $category->delete();

        $this->assertDatabaseMissing('categories', ['id' => $categoryId]);
    }

    /**
     * カテゴリが更新できることをテスト
     */
    public function test_category_can_be_updated(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create([
            'user_id' => $user->id,
            'name' => 'Original Name',
        ]);

        $category->update(['name' => 'Updated Name']);

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Updated Name',
        ]);
    }

    /**
     * カテゴリがユーザーに属することをテスト
     */
    public function test_category_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);

        $this->assertEquals($user->id, $category->user->id);
    }
}
