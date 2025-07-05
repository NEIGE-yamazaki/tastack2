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

    // 自分のカテゴリ → display_order順に取得
    $ownCategories = Category::withCount([
        'tasks as incomplete_tasks_count' => function ($query) {
            $query->where('is_done', false);
        }
    ])
    ->where('user_id', $user->id)
    ->orderBy('display_order') // 並び順を追加
    ->get();

    // 共有カテゴリ → 並び順は変更せずそのまま取得
    $sharedCategories = $user->sharedCategories()->withCount([
        'tasks as incomplete_tasks_count' => function ($query) {
            $query->where('is_done', false);
        }
    ])->get();

    return view('dashboard', compact('ownCategories', 'sharedCategories'));
}
    
}
