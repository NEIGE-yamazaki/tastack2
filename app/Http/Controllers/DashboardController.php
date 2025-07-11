<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Category;

class DashboardController extends Controller
{
    
public function index()
{
    $user = Auth::user();

    // 自分のカテゴリ → display_order順に取得（N+1問題を解消）
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

    // 共有カテゴリ → タスク情報も一度に取得（N+1問題を解消）
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

    return view('dashboard', compact('ownCategories', 'sharedCategories'));
}
    
}
