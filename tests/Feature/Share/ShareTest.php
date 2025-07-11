<?php

namespace Tests\Feature\Share;

use App\Models\User;
use App\Models\Category;
use App\Models\ShareGroup;
use App\Models\ShareGroupMember;
use App\Models\CategoryUserShare;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShareTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 共有グループを作成できることをテスト
     */
    public function test_share_group_can_be_created(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/share-groups', [
            'name' => 'Test Share Group',
            'description' => 'Test description',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('share_groups', [
            'name' => 'Test Share Group',
            'description' => 'Test description',
            'created_by' => $user->id,
        ]);
    }

    /**
     * 共有グループ名が必須であることをテスト
     */
    public function test_share_group_name_is_required(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/share-groups', [
            'name' => '',
            'description' => 'Test description',
        ]);

        $response->assertSessionHasErrors(['name']);
    }

    /**
     * 共有グループにメンバーを追加できることをテスト
     */
    public function test_member_can_be_added_to_share_group(): void
    {
        $creator = User::factory()->create();
        $member = User::factory()->create();
        $shareGroup = ShareGroup::factory()->create(['created_by' => $creator->id]);

        $response = $this->actingAs($creator)->post("/share-groups/{$shareGroup->id}/members", [
            'email' => $member->email,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('share_group_members', [
            'share_group_id' => $shareGroup->id,
            'user_id' => $member->id,
        ]);
    }

    /**
     * 存在しないメールアドレスでメンバーを追加できないことをテスト
     */
    public function test_member_with_invalid_email_cannot_be_added(): void
    {
        $creator = User::factory()->create();
        $shareGroup = ShareGroup::factory()->create(['created_by' => $creator->id]);

        $response = $this->actingAs($creator)->post("/share-groups/{$shareGroup->id}/members", [
            'email' => 'nonexistent@example.com',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    /**
     * カテゴリを共有グループに共有できることをテスト
     */
    public function test_category_can_be_shared_to_group(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);
        $shareGroup = ShareGroup::factory()->create(['created_by' => $user->id]);

        $response = $this->actingAs($user)->post("/categories/{$category->id}/share", [
            'share_group_id' => $shareGroup->id,
            'permission' => 'read',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('category_user_shares', [
            'category_id' => $category->id,
            'share_group_id' => $shareGroup->id,
            'permission' => 'read',
        ]);
    }

    /**
     * 権限レベルが正しく設定されることをテスト
     */
    public function test_permission_levels_are_correctly_set(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $user->id]);
        $shareGroup = ShareGroup::factory()->create(['created_by' => $user->id]);

        // 読み取り権限での共有
        $response = $this->actingAs($user)->post("/categories/{$category->id}/share", [
            'share_group_id' => $shareGroup->id,
            'permission' => 'read',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('category_user_shares', [
            'category_id' => $category->id,
            'share_group_id' => $shareGroup->id,
            'permission' => 'read',
        ]);

        // 編集権限での共有
        $category2 = Category::factory()->create(['user_id' => $user->id]);
        $response = $this->actingAs($user)->post("/categories/{$category2->id}/share", [
            'share_group_id' => $shareGroup->id,
            'permission' => 'edit',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('category_user_shares', [
            'category_id' => $category2->id,
            'share_group_id' => $shareGroup->id,
            'permission' => 'edit',
        ]);
    }

    /**
     * 共有されたカテゴリを閲覧できることをテスト
     */
    public function test_shared_category_can_be_viewed(): void
    {
        $owner = User::factory()->create();
        $member = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $owner->id]);
        $shareGroup = ShareGroup::factory()->create(['created_by' => $owner->id]);
        
        // メンバーを追加
        ShareGroupMember::factory()->create([
            'share_group_id' => $shareGroup->id,
            'user_id' => $member->id,
        ]);

        // カテゴリを共有
        CategoryUserShare::factory()->create([
            'category_id' => $category->id,
            'share_group_id' => $shareGroup->id,
            'permission' => 'read',
        ]);

        $response = $this->actingAs($member)->get("/shared-categories/{$category->id}");

        $response->assertStatus(200);
    }

    /**
     * 共有されていないカテゴリを閲覧できないことをテスト
     */
    public function test_non_shared_category_cannot_be_viewed(): void
    {
        $owner = User::factory()->create();
        $member = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $owner->id]);

        $response = $this->actingAs($member)->get("/shared-categories/{$category->id}");

        $response->assertStatus(404);
    }

    /**
     * 編集権限があるユーザーが共有されたカテゴリを編集できることをテスト
     */
    public function test_user_with_edit_permission_can_edit_shared_category(): void
    {
        $owner = User::factory()->create();
        $editor = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $owner->id]);
        $shareGroup = ShareGroup::factory()->create(['created_by' => $owner->id]);
        
        // エディターを追加
        ShareGroupMember::factory()->create([
            'share_group_id' => $shareGroup->id,
            'user_id' => $editor->id,
        ]);

        // カテゴリを編集権限で共有
        CategoryUserShare::factory()->create([
            'category_id' => $category->id,
            'share_group_id' => $shareGroup->id,
            'permission' => 'edit',
        ]);

        $response = $this->actingAs($editor)->put("/shared-categories/{$category->id}", [
            'name' => 'Updated Category Name',
            'description' => 'Updated description',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Updated Category Name',
            'description' => 'Updated description',
        ]);
    }

    /**
     * 読み取り権限のみのユーザーが共有されたカテゴリを編集できないことをテスト
     */
    public function test_user_with_read_only_permission_cannot_edit_shared_category(): void
    {
        $owner = User::factory()->create();
        $reader = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $owner->id]);
        $shareGroup = ShareGroup::factory()->create(['created_by' => $owner->id]);
        
        // リーダーを追加
        ShareGroupMember::factory()->create([
            'share_group_id' => $shareGroup->id,
            'user_id' => $reader->id,
        ]);

        // カテゴリを読み取り権限で共有
        CategoryUserShare::factory()->create([
            'category_id' => $category->id,
            'share_group_id' => $shareGroup->id,
            'permission' => 'read',
        ]);

        $response = $this->actingAs($reader)->put("/shared-categories/{$category->id}", [
            'name' => 'Updated Category Name',
            'description' => 'Updated description',
        ]);

        $response->assertStatus(403);
    }

    /**
     * 共有グループからメンバーを削除できることをテスト
     */
    public function test_member_can_be_removed_from_share_group(): void
    {
        $creator = User::factory()->create();
        $member = User::factory()->create();
        $shareGroup = ShareGroup::factory()->create(['created_by' => $creator->id]);
        $shareGroupMember = ShareGroupMember::factory()->create([
            'share_group_id' => $shareGroup->id,
            'user_id' => $member->id,
        ]);

        $response = $this->actingAs($creator)->delete("/share-groups/{$shareGroup->id}/members/{$member->id}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('share_group_members', [
            'share_group_id' => $shareGroup->id,
            'user_id' => $member->id,
        ]);
    }

    /**
     * 共有を取り消すことができることをテスト
     */
    public function test_category_sharing_can_be_revoked(): void
    {
        $owner = User::factory()->create();
        $category = Category::factory()->create(['user_id' => $owner->id]);
        $shareGroup = ShareGroup::factory()->create(['created_by' => $owner->id]);
        $categoryShare = CategoryUserShare::factory()->create([
            'category_id' => $category->id,
            'share_group_id' => $shareGroup->id,
            'permission' => 'read',
        ]);

        $response = $this->actingAs($owner)->delete("/categories/{$category->id}/share/{$shareGroup->id}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('category_user_shares', [
            'category_id' => $category->id,
            'share_group_id' => $shareGroup->id,
        ]);
    }

    /**
     * 共有グループを削除できることをテスト
     */
    public function test_share_group_can_be_deleted(): void
    {
        $creator = User::factory()->create();
        $shareGroup = ShareGroup::factory()->create(['created_by' => $creator->id]);

        $response = $this->actingAs($creator)->delete("/share-groups/{$shareGroup->id}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('share_groups', [
            'id' => $shareGroup->id,
        ]);
    }

    /**
     * 共有グループの作成者以外が削除できないことをテスト
     */
    public function test_non_creator_cannot_delete_share_group(): void
    {
        $creator = User::factory()->create();
        $otherUser = User::factory()->create();
        $shareGroup = ShareGroup::factory()->create(['created_by' => $creator->id]);

        $response = $this->actingAs($otherUser)->delete("/share-groups/{$shareGroup->id}");

        $response->assertStatus(403);
        $this->assertDatabaseHas('share_groups', [
            'id' => $shareGroup->id,
        ]);
    }
}
