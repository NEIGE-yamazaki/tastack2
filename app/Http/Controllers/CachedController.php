<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Models\Category;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;

class CachedController extends Controller
{
    /**
     * キャッシュ付きダッシュボード
     */
    public function cachedDashboard()
    {
        $user = Auth::user();
        $cacheKey = "dashboard_user_{$user->id}";
        
        $dashboardData = Cache::remember($cacheKey, 300, function () use ($user) { // 5分間キャッシュ
            // 自分のカテゴリ
            $ownCategories = Category::where('user_id', $user->id)
                ->with(['tasks' => function ($query) {
                    $query->select('id', 'category_id', 'is_done');
                }])
                ->withCount([
                    'tasks as incomplete_tasks_count' => function ($query) {
                        $query->where('is_done', false);
                    }
                ])
                ->orderBy('display_order')
                ->get();

            // 共有カテゴリ
            $sharedCategories = $user->sharedCategories()
                ->with(['tasks' => function ($query) {
                    $query->select('id', 'category_id', 'is_done');
                }])
                ->withCount([
                    'tasks as incomplete_tasks_count' => function ($query) {
                        $query->where('is_done', false);
                    }
                ])
                ->get();

            return [
                'ownCategories' => $ownCategories,
                'sharedCategories' => $sharedCategories,
                'cached_at' => Carbon::now()->toISOString(),
            ];
        });

        return view('dashboard-cached', $dashboardData);
    }

    /**
     * キャッシュ付きカテゴリ統計
     */
    public function cachedCategoryStats()
    {
        $user = Auth::user();
        $cacheKey = "category_stats_user_{$user->id}";
        
        $stats = Cache::remember($cacheKey, 600, function () use ($user) { // 10分間キャッシュ
            $categories = Category::where('user_id', $user->id)
                ->withCount([
                    'tasks as total_tasks',
                    'tasks as completed_tasks' => function ($query) {
                        $query->where('is_done', true);
                    },
                    'tasks as pending_tasks' => function ($query) {
                        $query->where('is_done', false);
                    }
                ])
                ->get();

            $totalTasks = $categories->sum('total_tasks');
            $completedTasks = $categories->sum('completed_tasks');
            $pendingTasks = $categories->sum('pending_tasks');
            $completionRate = $totalTasks > 0 ? ($completedTasks / $totalTasks) * 100 : 0;

            return [
                'total_categories' => $categories->count(),
                'total_tasks' => $totalTasks,
                'completed_tasks' => $completedTasks,
                'pending_tasks' => $pendingTasks,
                'completion_rate' => round($completionRate, 2),
                'categories' => $categories,
                'cached_at' => Carbon::now()->toISOString(),
            ];
        });

        return response()->json($stats);
    }

    /**
     * キャッシュ付きユーザー統計
     */
    public function cachedUserStats()
    {
        $user = Auth::user();
        $cacheKey = "user_stats_{$user->id}";
        
        $stats = Cache::remember($cacheKey, 1800, function () use ($user) { // 30分間キャッシュ
            // 今日のタスク完了数
            $todayCompletedTasks = Task::whereHas('category', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->where('is_done', true)
                ->whereDate('updated_at', today())
                ->count();

            // 今週のタスク完了数
            $weekCompletedTasks = Task::whereHas('category', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->where('is_done', true)
                ->whereBetween('updated_at', [
                    Carbon::now()->startOfWeek(),
                    Carbon::now()->endOfWeek()
                ])
                ->count();

            // 今月のタスク完了数
            $monthCompletedTasks = Task::whereHas('category', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->where('is_done', true)
                ->whereMonth('updated_at', Carbon::now()->month)
                ->whereYear('updated_at', Carbon::now()->year)
                ->count();

            // 期限切れタスク数
            $overdueTasks = Task::whereHas('category', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->where('is_done', false)
                ->where('due_date', '<', Carbon::now())
                ->count();

            return [
                'today_completed' => $todayCompletedTasks,
                'week_completed' => $weekCompletedTasks,
                'month_completed' => $monthCompletedTasks,
                'overdue_tasks' => $overdueTasks,
                'ai_advisor_used_today' => $user->ai_advisor_used_today,
                'ai_advisor_limit' => $user->ai_advisor_limit_per_day,
                'cached_at' => Carbon::now()->toISOString(),
            ];
        });

        return response()->json($stats);
    }

    /**
     * キャッシュクリア
     */
    public function clearCache(Request $request)
    {
        $user = Auth::user();
        $type = $request->input('type', 'all');

        switch ($type) {
            case 'dashboard':
                Cache::forget("dashboard_user_{$user->id}");
                break;
            case 'stats':
                Cache::forget("category_stats_user_{$user->id}");
                Cache::forget("user_stats_{$user->id}");
                break;
            case 'all':
                Cache::forget("dashboard_user_{$user->id}");
                Cache::forget("category_stats_user_{$user->id}");
                Cache::forget("user_stats_{$user->id}");
                break;
        }

        return response()->json(['message' => 'キャッシュをクリアしました']);
    }

    /**
     * タスク作成・更新時のキャッシュ無効化
     */
    public function invalidateUserCache($userId)
    {
        Cache::forget("dashboard_user_{$userId}");
        Cache::forget("category_stats_user_{$userId}");
        Cache::forget("user_stats_{$userId}");
    }

    /**
     * 頻繁に使用される共有カテゴリのキャッシュ
     */
    public function cachedSharedCategories()
    {
        $user = Auth::user();
        $cacheKey = "shared_categories_user_{$user->id}";
        
        $sharedCategories = Cache::remember($cacheKey, 600, function () use ($user) { // 10分間キャッシュ
            return $user->sharedCategories()
                ->wherePivot('is_confirmed', true)
                ->withPivot(['permission', 'confirmation_token'])
                ->with(['tasks' => function ($query) {
                    $query->select('id', 'category_id', 'is_done');
                }])
                ->withCount([
                    'tasks as incomplete_tasks_count' => function ($query) {
                        $query->where('is_done', false);
                    }
                ])
                ->get();
        });

        return response()->json([
            'shared_categories' => $sharedCategories,
            'cached_at' => Carbon::now()->toISOString(),
        ]);
    }

    /**
     * 人気カテゴリのキャッシュ（全ユーザー向け）
     */
    public function cachedPopularCategories()
    {
        $popularCategories = Cache::remember('popular_categories', 3600, function () { // 1時間キャッシュ
            return Category::withCount([
                    'tasks as total_tasks',
                    'sharedUsers as shared_count'
                ])
                ->where('is_public_shared', true)
                ->orderBy('shared_count', 'desc')
                ->orderBy('total_tasks', 'desc')
                ->take(10)
                ->get();
        });

        return response()->json([
            'popular_categories' => $popularCategories,
            'cached_at' => Carbon::now()->toISOString(),
        ]);
    }
}
