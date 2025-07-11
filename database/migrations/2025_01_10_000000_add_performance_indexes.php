<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            // ユーザーIDでの検索を高速化
            $table->index(['user_id', 'display_order'], 'idx_categories_user_display_order');
            
            // 共有トークンでの検索を高速化
            $table->index('public_share_token', 'idx_categories_public_share_token');
            
            // 名前での検索を高速化（部分一致検索用）
            $table->index('name', 'idx_categories_name');
        });
        
        Schema::table('tasks', function (Blueprint $table) {
            // カテゴリIDと完了状態での検索を高速化
            $table->index(['category_id', 'is_done'], 'idx_tasks_category_done');
            
            // カテゴリIDと作成日での検索を高速化（タスク一覧表示用）
            $table->index(['category_id', 'created_at'], 'idx_tasks_category_created');
            
            // 期限日での検索を高速化
            $table->index('due_date', 'idx_tasks_due_date');
            
            // 完了状態での検索を高速化
            $table->index('is_done', 'idx_tasks_is_done');
        });
        
        Schema::table('category_user_shares', function (Blueprint $table) {
            // カテゴリIDとユーザーIDでの検索を高速化
            $table->index(['category_id', 'shared_user_id'], 'idx_category_user_shares_category_user');
            
            // 共有ユーザーIDでの検索を高速化
            $table->index('shared_user_id', 'idx_category_user_shares_shared_user');
            
            // 確認済みフラグでの検索を高速化
            $table->index('is_confirmed', 'idx_category_user_shares_is_confirmed');
            
            // 確認トークンでの検索を高速化
            $table->index('confirmation_token', 'idx_category_user_shares_confirmation_token');
            
            // 複合インデックス（権限と確認状態）
            $table->index(['permission', 'is_confirmed'], 'idx_category_user_shares_permission_confirmed');
        });
        
        Schema::table('share_groups', function (Blueprint $table) {
            // ユーザーIDでの検索を高速化
            $table->index('user_id', 'idx_share_groups_user_id');
            
            // 名前での検索を高速化
            $table->index('name', 'idx_share_groups_name');
        });
        
        Schema::table('share_group_members', function (Blueprint $table) {
            // 共有グループIDでの検索を高速化
            $table->index('share_group_id', 'idx_share_group_members_share_group_id');
            
            // ユーザーIDでの検索を高速化
            $table->index('user_id', 'idx_share_group_members_user_id');
            
            // 識別子（メール/アカウントID）での検索を高速化
            $table->index('identifier', 'idx_share_group_members_identifier');
        });
        
        Schema::table('users', function (Blueprint $table) {
            // アカウントIDでの検索を高速化
            $table->index('account_id', 'idx_users_account_id');
            
            // プロバイダーでの検索を高速化
            $table->index(['provider', 'provider_id'], 'idx_users_provider_provider_id');
            
            // AIアドバイザー使用状況での検索を高速化
            $table->index('ai_advisor_last_used_at', 'idx_users_ai_advisor_last_used_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropIndex('idx_categories_user_display_order');
            $table->dropIndex('idx_categories_public_share_token');
            $table->dropIndex('idx_categories_name');
        });
        
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropIndex('idx_tasks_category_done');
            $table->dropIndex('idx_tasks_category_created');
            $table->dropIndex('idx_tasks_due_date');
            $table->dropIndex('idx_tasks_is_done');
        });
        
        Schema::table('category_user_shares', function (Blueprint $table) {
            $table->dropIndex('idx_category_user_shares_category_user');
            $table->dropIndex('idx_category_user_shares_shared_user');
            $table->dropIndex('idx_category_user_shares_is_confirmed');
            $table->dropIndex('idx_category_user_shares_confirmation_token');
            $table->dropIndex('idx_category_user_shares_permission_confirmed');
        });
        
        Schema::table('share_groups', function (Blueprint $table) {
            $table->dropIndex('idx_share_groups_user_id');
            $table->dropIndex('idx_share_groups_name');
        });
        
        Schema::table('share_group_members', function (Blueprint $table) {
            $table->dropIndex('idx_share_group_members_share_group_id');
            $table->dropIndex('idx_share_group_members_user_id');
            $table->dropIndex('idx_share_group_members_identifier');
        });
        
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('idx_users_account_id');
            $table->dropIndex('idx_users_provider_provider_id');
            $table->dropIndex('idx_users_ai_advisor_last_used_at');
        });
    }
};
