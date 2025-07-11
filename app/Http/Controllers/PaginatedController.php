<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Category;
use App\Models\Task;
use App\Models\CategoryUserShare;

class PaginatedController extends Controller
{
    /**
     * ページネーション付きカテゴリ一覧
     */
    public function paginatedCategories(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');
        
        $query = Category::where('user_id', Auth::id())
            ->with(['tasks' => function ($query) {
                $query->select('id', 'category_id', 'is_done');
            }])
            ->withCount([
                'tasks as incomplete_tasks_count' => function ($query) {
                    $query->where('is_done', false);
                }
            ])
            ->orderBy('display_order');

        if ($search) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        $categories = $query->paginate($perPage);

        if ($request->expectsJson()) {
            return response()->json($categories);
        }

        return view('categories.paginated', compact('categories'));
    }

    /**
     * ページネーション付きタスク一覧
     */
    public function paginatedTasks(Request $request, Category $category)
    {
        $user = Auth::user();
        $perPage = $request->input('per_page', 20);
        $search = $request->input('search');
        $status = $request->input('status'); // 'completed', 'pending', 'all'

        // アクセス権限チェック
        $hasAccess = $category->user_id === $user->id ||
            CategoryUserShare::where('category_id', $category->id)
                ->where('shared_user_id', $user->id)
                ->where('is_confirmed', true)
                ->exists();

        if (!$hasAccess) {
            abort(403);
        }

        $query = $category->tasks()
            ->with(['category:id,name,icon_path'])
            ->orderBy('created_at', 'desc');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('note', 'like', '%' . $search . '%');
            });
        }

        if ($status === 'completed') {
            $query->where('is_done', true);
        } elseif ($status === 'pending') {
            $query->where('is_done', false);
        }

        $tasks = $query->paginate($perPage);

        if ($request->expectsJson()) {
            return response()->json($tasks);
        }

        return view('tasks.paginated', compact('tasks', 'category'));
    }

    /**
     * ページネーション付き共有カテゴリ一覧
     */
    public function paginatedSharedCategories(Request $request)
    {
        $user = Auth::user();
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');

        $query = $user->sharedCategories()
            ->wherePivot('is_confirmed', true)
            ->withPivot(['permission', 'confirmation_token'])
            ->with(['tasks' => function ($query) {
                $query->select('id', 'category_id', 'is_done');
            }])
            ->withCount([
                'tasks as incomplete_tasks_count' => function ($query) {
                    $query->where('is_done', false);
                }
            ]);

        if ($search) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        $sharedCategories = $query->paginate($perPage);

        if ($request->expectsJson()) {
            return response()->json($sharedCategories);
        }

        return view('categories.shared-paginated', compact('sharedCategories'));
    }

    /**
     * 無限スクロール用のタスク取得
     */
    public function infiniteScrollTasks(Request $request, Category $category)
    {
        $user = Auth::user();
        $page = $request->input('page', 1);
        $perPage = 20;

        // アクセス権限チェック
        $hasAccess = $category->user_id === $user->id ||
            CategoryUserShare::where('category_id', $category->id)
                ->where('shared_user_id', $user->id)
                ->where('is_confirmed', true)
                ->exists();

        if (!$hasAccess) {
            return response()->json(['error' => '権限がありません'], 403);
        }

        $tasks = $category->tasks()
            ->with(['category:id,name,icon_path'])
            ->orderBy('created_at', 'desc')
            ->offset(($page - 1) * $perPage)
            ->limit($perPage)
            ->get();

        return response()->json([
            'tasks' => $tasks,
            'has_more' => $tasks->count() === $perPage,
            'current_page' => $page,
            'next_page' => $page + 1
        ]);
    }

    /**
     * 仮想スクロール用のタスクデータ取得
     */
    public function virtualScrollTasks(Request $request, Category $category)
    {
        $user = Auth::user();
        $startIndex = $request->input('start', 0);
        $endIndex = $request->input('end', 50);
        $limit = $endIndex - $startIndex;

        // アクセス権限チェック
        $hasAccess = $category->user_id === $user->id ||
            CategoryUserShare::where('category_id', $category->id)
                ->where('shared_user_id', $user->id)
                ->where('is_confirmed', true)
                ->exists();

        if (!$hasAccess) {
            return response()->json(['error' => '権限がありません'], 403);
        }

        $totalCount = $category->tasks()->count();
        
        $tasks = $category->tasks()
            ->select('id', 'category_id', 'title', 'due_date', 'is_done', 'created_at')
            ->orderBy('created_at', 'desc')
            ->offset($startIndex)
            ->limit($limit)
            ->get();

        return response()->json([
            'tasks' => $tasks,
            'total_count' => $totalCount,
            'start_index' => $startIndex,
            'end_index' => min($startIndex + $tasks->count(), $totalCount)
        ]);
    }
}
