<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Models\User;
use App\Models\Category;
use App\Models\Task;
use App\Models\CategoryUserShare;
use Carbon\Carbon;

class PerformanceBenchmark extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'performance:benchmark {--users=100} {--categories=50} {--tasks=1000} {--shares=200}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'パフォーマンスベンチマークを実行します';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('パフォーマンスベンチマークを開始します...');

        $users = (int) $this->option('users');
        $categories = (int) $this->option('categories');
        $tasks = (int) $this->option('tasks');
        $shares = (int) $this->option('shares');

        $this->info("データ準備: ユーザー{$users}件, カテゴリ{$categories}件, タスク{$tasks}件, 共有{$shares}件");

        // データ準備
        $this->prepareTestData($users, $categories, $tasks, $shares);

        // ベンチマーク実行
        $this->runBenchmarks();

        $this->info('ベンチマーク完了');
    }

    /**
     * テストデータの準備
     */
    private function prepareTestData(int $users, int $categories, int $tasks, int $shares): void
    {
        $this->info('テストデータを準備中...');

        // ユーザー作成
        $this->withProgressBar(range(1, $users), function ($i) {
            User::create([
                'name' => "Test User {$i}",
                'email' => "test{$i}@example.com",
                'password' => bcrypt('password'),
                'account_id' => "test_account_{$i}",
            ]);
        });

        $this->newLine();

        // カテゴリ作成
        $userIds = User::pluck('id')->toArray();
        $this->withProgressBar(range(1, $categories), function ($i) use ($userIds) {
            Category::create([
                'user_id' => $userIds[array_rand($userIds)],
                'name' => "Test Category {$i}",
                'icon_path' => 'default_icons/icon1.png',
                'display_order' => $i,
            ]);
        });

        $this->newLine();

        // タスク作成
        $categoryIds = Category::pluck('id')->toArray();
        $this->withProgressBar(range(1, $tasks), function ($i) use ($categoryIds) {
            Task::create([
                'category_id' => $categoryIds[array_rand($categoryIds)],
                'title' => "Test Task {$i}",
                'due_date' => rand(0, 1) ? Carbon::now()->addDays(rand(1, 30)) : null,
                'note' => "Test note for task {$i}",
                'is_done' => rand(0, 1),
            ]);
        });

        $this->newLine();

        // 共有作成
        $this->withProgressBar(range(1, $shares), function ($i) use ($categoryIds, $userIds) {
            CategoryUserShare::create([
                'category_id' => $categoryIds[array_rand($categoryIds)],
                'shared_user_id' => $userIds[array_rand($userIds)],
                'permission' => ['view', 'edit', 'full'][array_rand(['view', 'edit', 'full'])],
                'confirmation_token' => \Str::random(32),
                'is_confirmed' => rand(0, 1),
            ]);
        });

        $this->newLine();
        $this->info('テストデータ準備完了');
    }

    /**
     * ベンチマーク実行
     */
    private function runBenchmarks(): void
    {
        $this->info('ベンチマークを実行中...');

        $results = [];

        // 1. カテゴリ一覧取得（最適化前）
        $results['categories_unoptimized'] = $this->benchmarkQuery(
            'カテゴリ一覧取得（最適化前）',
            function () {
                return Category::with(['user'])
                    ->withCount('tasks')
                    ->orderBy('display_order')
                    ->get();
            }
        );

        // 2. カテゴリ一覧取得（最適化後）
        $results['categories_optimized'] = $this->benchmarkQuery(
            'カテゴリ一覧取得（最適化後）',
            function () {
                return Category::with(['tasks' => function ($query) {
                        $query->select('id', 'category_id', 'is_done');
                    }])
                    ->withCount(['tasks as incomplete_tasks_count' => function ($query) {
                        $query->where('is_done', false);
                    }])
                    ->orderBy('display_order')
                    ->get();
            }
        );

        // 3. タスク一覧取得（最適化前）
        $results['tasks_unoptimized'] = $this->benchmarkQuery(
            'タスク一覧取得（最適化前）',
            function () {
                return Task::with(['category'])
                    ->orderBy('created_at', 'desc')
                    ->get();
            }
        );

        // 4. タスク一覧取得（最適化後）
        $results['tasks_optimized'] = $this->benchmarkQuery(
            'タスク一覧取得（最適化後）',
            function () {
                return Task::with(['category:id,name,icon_path'])
                    ->orderBy('created_at', 'desc')
                    ->get();
            }
        );

        // 5. 共有カテゴリ取得（最適化前）
        $user = User::first();
        $results['shared_categories_unoptimized'] = $this->benchmarkQuery(
            '共有カテゴリ取得（最適化前）',
            function () use ($user) {
                return $user->sharedCategories()->get();
            }
        );

        // 6. 共有カテゴリ取得（最適化後）
        $results['shared_categories_optimized'] = $this->benchmarkQuery(
            '共有カテゴリ取得（最適化後）',
            function () use ($user) {
                return $user->sharedCategories()
                    ->with(['tasks' => function ($query) {
                        $query->select('id', 'category_id', 'is_done');
                    }])
                    ->withCount(['tasks as incomplete_tasks_count' => function ($query) {
                        $query->where('is_done', false);
                    }])
                    ->get();
            }
        );

        // 7. キャッシュ付きクエリ
        $results['cached_query'] = $this->benchmarkQuery(
            'キャッシュ付きクエリ',
            function () {
                return Cache::remember('benchmark_cache', 60, function () {
                    return Category::with(['tasks' => function ($query) {
                            $query->select('id', 'category_id', 'is_done');
                        }])
                        ->withCount(['tasks as incomplete_tasks_count' => function ($query) {
                            $query->where('is_done', false);
                        }])
                        ->orderBy('display_order')
                        ->get();
                });
            }
        );

        // 8. ページネーション
        $results['pagination'] = $this->benchmarkQuery(
            'ページネーション',
            function () {
                return Task::with(['category:id,name,icon_path'])
                    ->orderBy('created_at', 'desc')
                    ->paginate(20);
            }
        );

        // 結果出力
        $this->displayResults($results);
    }

    /**
     * クエリのベンチマーク
     */
    private function benchmarkQuery(string $name, callable $callback): array
    {
        $queryCount = 0;
        $queryTime = 0;

        DB::listen(function ($query) use (&$queryCount, &$queryTime) {
            $queryCount++;
            $queryTime += $query->time;
        });

        $startTime = microtime(true);
        $startMemory = memory_get_usage(true);

        $result = $callback();

        $endTime = microtime(true);
        $endMemory = memory_get_usage(true);

        return [
            'name' => $name,
            'execution_time' => round(($endTime - $startTime) * 1000, 2), // ms
            'memory_usage' => round(($endMemory - $startMemory) / 1024 / 1024, 2), // MB
            'query_count' => $queryCount,
            'query_time' => round($queryTime, 2), // ms
            'result_count' => is_countable($result) ? count($result) : 1,
        ];
    }

    /**
     * 結果表示
     */
    private function displayResults(array $results): void
    {
        $this->newLine();
        $this->info('=== パフォーマンスベンチマーク結果 ===');
        $this->newLine();

        $headers = ['テスト項目', '実行時間(ms)', 'メモリ使用量(MB)', 'クエリ数', 'クエリ時間(ms)', '結果数'];
        $rows = [];

        foreach ($results as $result) {
            $rows[] = [
                $result['name'],
                $result['execution_time'],
                $result['memory_usage'],
                $result['query_count'],
                $result['query_time'],
                $result['result_count'],
            ];
        }

        $this->table($headers, $rows);

        // 改善率計算
        $this->newLine();
        $this->info('=== 最適化による改善率 ===');

        $improvements = [
            'カテゴリ一覧' => $this->calculateImprovement(
                $results['categories_unoptimized'],
                $results['categories_optimized']
            ),
            'タスク一覧' => $this->calculateImprovement(
                $results['tasks_unoptimized'],
                $results['tasks_optimized']
            ),
            '共有カテゴリ' => $this->calculateImprovement(
                $results['shared_categories_unoptimized'],
                $results['shared_categories_optimized']
            ),
        ];

        foreach ($improvements as $name => $improvement) {
            $this->info("{$name}: 実行時間 {$improvement['execution_time']}%, クエリ数 {$improvement['query_count']}%");
        }
    }

    /**
     * 改善率計算
     */
    private function calculateImprovement(array $before, array $after): array
    {
        $executionTimeImprovement = $before['execution_time'] > 0 
            ? round((($before['execution_time'] - $after['execution_time']) / $before['execution_time']) * 100, 1)
            : 0;

        $queryCountImprovement = $before['query_count'] > 0
            ? round((($before['query_count'] - $after['query_count']) / $before['query_count']) * 100, 1)
            : 0;

        return [
            'execution_time' => $executionTimeImprovement,
            'query_count' => $queryCountImprovement,
        ];
    }
}
