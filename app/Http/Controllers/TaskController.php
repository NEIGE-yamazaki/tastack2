<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\CategoryUserShare;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{

public function toggle(Request $request, Task $task)
{
    $user = Auth::user();

    // カテゴリ情報を事前に取得してクエリを最適化
    $task->load(['category.user', 'category.sharedUsers' => function ($query) use ($user) {
        $query->where('shared_user_id', $user->id)
              ->whereIn('permission', ['edit', 'full'])
              ->where('is_confirmed', true);
    }]);

    $isOwner = $task->category->user_id === $user->id;
    $hasPermission = $task->category->sharedUsers->isNotEmpty();

    if (!$isOwner && !$hasPermission) {
        return response()->json(['error' => '権限がありません'], 403);
    }

    $task->is_done = !$task->is_done;
    $task->save();

    return response()->json([
        'message' => 'タスクの完了状態を変更しました',
        'is_done' => $task->is_done,
        'task_id' => $task->id
    ]);
}
    
public function update(Request $request, Task $task)
{
    $user = Auth::user();
    
    // カテゴリ情報と共有情報を事前に取得してクエリを最適化
    $task->load(['category.user', 'category.sharedUsers' => function ($query) use ($user) {
        $query->where('shared_user_id', $user->id)
              ->where('permission', 'full')
              ->where('is_confirmed', true);
    }]);

    $isOwner = $task->category->user_id === $user->id;
    $hasFullPermission = $task->category->sharedUsers->isNotEmpty();

    if (!$isOwner && !$hasFullPermission) {
        return response()->json(['error' => '権限がありません'], 403);
    }

    $request->validate([
        'title' => 'required|string|max:255',
        'due_date' => 'nullable|date',
        'note' => 'nullable|string',
    ]);

    $task->update([
        'title' => $request->title,
        'due_date' => $request->due_date,
        'note' => $request->note,
    ]);

    return response()->json(['message' => 'タスクを更新しました']);
}

public function destroy(Task $task)
{
    $user = Auth::user();

    // カテゴリ情報と共有情報を事前に取得してクエリを最適化
    $task->load(['category.user', 'category.sharedUsers' => function ($query) use ($user) {
        $query->where('shared_user_id', $user->id)
              ->where('permission', 'full')
              ->where('is_confirmed', true);
    }]);

    $isOwner = $task->category->user_id === $user->id;
    $hasPermission = $task->category->sharedUsers->isNotEmpty();

    if (!$isOwner && !$hasPermission) {
        return response()->json(['error' => '権限がありません'], 403);
    }

    $task->delete();

    return response()->json(['message' => 'タスクを削除しました']);
}
    
}