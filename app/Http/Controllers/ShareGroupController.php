<?php

namespace App\Http\Controllers;

use App\Models\ShareGroup;
use App\Models\ShareGroupMember;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShareGroupController extends Controller
{
    /**
     * 一覧表示
     */
    public function index()
    {
        $groups = ShareGroup::with('members.user')
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('share_groups.index', compact('groups'));
    }

    /**
     * 作成処理
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50',
            'identifiers' => 'required|array|min:1',
            'identifiers.*' => 'required|string|max:255',
        ]);

        $group = ShareGroup::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
        ]);

        $this->storeMembers($group, $request->identifiers);

        return redirect()->back()->with('success', 'グループを作成しました');
    }

    /**
     * 編集フォーム表示
     */
    public function edit(ShareGroup $group)
    {
        if ($group->user_id !== Auth::id()) {
            abort(403);
        }

        $group->load('members.user');
        return view('share_groups.edit', compact('group'));
    }

    /**
     * 更新処理
     */
    public function update(Request $request, ShareGroup $group)
    {
        if ($group->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:50',
            'identifiers' => 'required|array|min:1',
            'identifiers.*' => 'required|string|max:255',
        ]);

        $group->update(['name' => $request->name]);

        // 古いメンバーを削除し再登録
        $group->members()->delete();
        $this->storeMembers($group, $request->identifiers);

        return redirect()->route('share-groups.index')->with('success', 'グループを更新しました');
    }

    /**
     * 削除処理
     */
    public function destroy(ShareGroup $group)
    {
        if ($group->user_id !== Auth::id()) {
            abort(403);
        }

        $group->delete();

        return redirect()->back()->with('success', 'グループを削除しました');
    }

    /**
     * メンバー保存共通処理
     */
private function storeMembers(ShareGroup $group, array $identifiers)
{
    $skipped = [];
    $addedUserIds = [];

    foreach ($identifiers as $rawIdentifier) {
        $identifier = trim(mb_convert_kana($rawIdentifier, 'as'));

        if ($identifier === '') {
            continue; // 空欄スキップ
        }

        $user = User::where('email', $identifier)
            ->orWhere('account_id', $identifier)
            ->first();

        if (!$user) {
            $skipped[] = $identifier;
            continue;
        }

        if (in_array($user->id, $addedUserIds)) {
            continue; // 重複スキップ
        }

        $group->members()->create([
            'identifier' => $user->account_id ?? $user->email,
            'user_id' => $user->id,
        ]);

        $addedUserIds[] = $user->id;
    }

    if (!empty($skipped)) {
        session()->flash('error', '以下のユーザーは存在しないためスキップされました: ' . implode(', ', $skipped));
    }
}
    
}
