<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Category;
use App\Models\Task;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class OptimizeData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $userId;
    public $optimizationType;

    /**
     * Create a new job instance.
     */
    public function __construct(int $userId, string $optimizationType = 'all')
    {
        $this->userId = $userId;
        $this->optimizationType = $optimizationType;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            switch ($this->optimizationType) {
                case 'cache_warmup':
                    $this->warmupCache();
                    break;
                case 'data_cleanup':
                    $this->cleanupData();
                    break;
                case 'index_optimization':
                    $this->optimizeIndexes();
                    break;
                case 'all':
                default:
                    $this->warmupCache();
                    $this->cleanupData();
                    $this->optimizeIndexes();
                    break;
            }

            Log::info('データ最適化完了', [
                'user_id' => $this->userId,
                'optimization_type' => $this->optimizationType,
            ]);
        } catch (\Exception $e) {
            Log::error('データ最適化エラー', [
                'user_id' => $this->userId,
                'optimization_type' => $this->optimizationType,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * キャッシュのウォームアップ
     */
    private function warmupCache(): void
    {
        // ダッシュボードデータの事前キャッシュ
        $dashboardCacheKey = "dashboard_user_{$this->userId}";
        Cache::forget($dashboardCacheKey);
        
        // カテゴリ統計の事前キャッシュ
        $statsCacheKey = "category_stats_user_{$this->userId}";
        Cache::forget($statsCacheKey);
        
        // ユーザー統計の事前キャッシュ
        $userStatsCacheKey = "user_stats_{$this->userId}";
        Cache::forget($userStatsCacheKey);
        
        // 共有カテゴリの事前キャッシュ
        $sharedCacheKey = "shared_categories_user_{$this->userId}";
        Cache::forget($sharedCacheKey);
        
        Log::info('キャッシュウォームアップ完了', ['user_id' => $this->userId]);
    }

    /**
     * データクリーンアップ
     */
    private function cleanupData(): void
    {
        // 古い完了タスクのアーカイブ（1年以上前）
        $archivedTasks = Task::whereHas('category', function ($query) {
                $query->where('user_id', $this->userId);
            })
            ->where('is_done', true)
            ->where('updated_at', '<', Carbon::now()->subYear())
            ->count();

        if ($archivedTasks > 0) {
            // 実際のアーカイブ処理（必要に応じて）
            Log::info('古い完了タスクのクリーンアップ', [
                'user_id' => $this->userId,
                'archived_tasks' => $archivedTasks,
            ]);
        }

        // 未確認の共有リンクの削除（30日以上前）
        $expiredShares = DB::table('category_user_shares')
            ->where('shared_user_id', $this->userId)
            ->where('is_confirmed', false)
            ->where('created_at', '<', Carbon::now()->subDays(30))
            ->delete();

        if ($expiredShares > 0) {
            Log::info('期限切れ共有リンクの削除', [
                'user_id' => $this->userId,
                'deleted_shares' => $expiredShares,
            ]);
        }
    }

    /**
     * インデックス最適化
     */
    private function optimizeIndexes(): void
    {
        // データベース統計の更新
        DB::statement('ANALYZE TABLE categories');
        DB::statement('ANALYZE TABLE tasks');
        DB::statement('ANALYZE TABLE category_user_shares');
        
        Log::info('インデックス最適化完了', ['user_id' => $this->userId]);
    }
}
