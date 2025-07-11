<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\Category;
use App\Models\Task;
use App\Models\User;
use App\Models\CategoryUserShare;
use App\Notifications\CategoryShareInvitation;
use App\Models\ShareGroup;

use Google_Client;
use Google_Service_Calendar;
use Google_Service_Calendar_Event;
use Carbon\Carbon;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class CategoryController extends Controller
{

// カテゴリ一覧
public function index()
{
    $categories = Category::where('user_id', Auth::id())
        ->with(['tasks' => function ($query) {
            $query->select('id', 'category_id', 'is_done');
        }])
        ->withCount(['tasks as incomplete_tasks_count' => function ($query) {
            $query->where('is_done', false);
        }])
        ->orderBy('display_order')
        ->get();

    return view('categories.index', compact('categories'));
}

// カテゴリ一覧（並べ替え）
public function reorder(Request $request)
{
    foreach ($request->orders as $item) {
        Category::where('id', $item['id'])
            ->where('user_id', Auth::id())
            ->update(['display_order' => $item['order']]);
    }

    return response()->json(['status' => 'ok']);
}

// カテゴリ作成
public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:50',
        'icon' => 'nullable|image|max:5120', // 単位: KB → 5MB
    ], [
        'icon.max' => 'アイコン画像は5MB以内でアップロードしてください。',
        'icon.image' => 'アイコン画像には有効な画像ファイルを指定してください。',
    ]);

    try {
        $iconPath = null;

        if ($request->hasFile('icon') && $request->file('icon')->isValid()) {
            $manager = new ImageManager(new \Intervention\Image\Drivers\Gd\Driver());

            $image = $manager->read($request->file('icon')->getPathname());

            // 元サイズを取得
            $originalWidth = $image->width();
            $originalHeight = $image->height();

            // 横幅200pxに合わせた縮小後の縦サイズを計算
            $scaledHeight = intval(($originalHeight / $originalWidth) * 200);

            // 明示的にリサイズ（縦横比を維持）
            $resized = $image->resize(200, $scaledHeight);

            // JPEG形式に変換（画質75）
            $jpeg = $resized->toJpeg(75);

            // ファイル名生成・保存
            $filename = 'category_icons/' . uniqid('cat_') . '.jpg';

            if (!Storage::disk('public')->put($filename, $jpeg->toString())) {
                throw new \Exception("画像の保存に失敗しました。");
            }

            $iconPath = $filename;
        } else {
            $defaultImages = [
                'default_icons/icon1.png',
                'default_icons/icon2.png',
                'default_icons/icon3.png',
                'default_icons/icon4.png',
                'default_icons/icon5.png',
                'default_icons/icon6.png',
                'default_icons/icon7.png',
                'default_icons/icon8.png',
                'default_icons/icon9.png',
                'default_icons/icon10.png',
            ];
            $iconPath = $defaultImages[array_rand($defaultImages)];
        }

        Category::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'icon_path' => $iconPath,
        ]);

        return redirect()->back()->with('success', 'カテゴリを追加しました');
    } catch (\Throwable $e) {
        \Log::error('カテゴリ作成エラー: ' . $e->getMessage(), [
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ]);
        return redirect()->back()->with('error', 'カテゴリの作成中にエラーが発生しました。');
    }
}

// カテゴリ編集
public function update(Request $request, Category $category)
{
    if ($category->user_id !== Auth::id()) {
        abort(403);
    }

    $request->validate([
        'name' => 'required|string|max:50',
        'icon' => 'nullable|image|max:5120', // ← 追加
    ]);

    $category->name = $request->name;
    
if ($request->hasFile('icon') && $request->file('icon')->isValid()) {
    $manager = new ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
    $image = $manager->read($request->file('icon')->getPathname());

    $originalWidth = $image->width();
    $originalHeight = $image->height();
    $scaledHeight = intval(($originalHeight / $originalWidth) * 200);

    $resized = $image->resize(200, $scaledHeight);
    $jpeg = $resized->toJpeg(75);

    $filename = 'category_icons/' . uniqid('cat_') . '.jpg';

    if (!Storage::disk('public')->put($filename, $jpeg->toString())) {
        throw new \Exception("画像の保存に失敗しました。");
    }

    // 古い画像がカスタム画像（＝defaultではない）なら削除
    if ($category->icon_path && !str_starts_with($category->icon_path, 'default_icons/')) {
        Storage::disk('public')->delete($category->icon_path);
    }

    $category->icon_path = $filename;
}
    
    $category->save();
    
return response()->json([
    'id' => $category->id,
    'name' => $category->name,
    'icon_path' => $category->icon_path,
    'incomplete_tasks_count' => $category->tasks()->where('is_done', false)->count(),
]);
    
}

// カテゴリ削除
public function destroy(Category $category)
{
    $user = Auth::user();

    // オーナー確認
    if ($category->user_id !== $user->id) {
        abort(403);
    }

    // 紐づくタスクを削除
    $category->tasks()->delete();

    // カテゴリ共有情報も削除
    $category->sharedUsers()->detach();

    // カテゴリ自体を削除
    $category->delete();

    // Ajaxでの削除要求の場合（JSからfetchで来る場合）
    if (request()->ajax()) {
        return response()->json(['message' => '削除しました']);
    }

    // 通常の削除（リダイレクト）
    return redirect()->route('categories.index')->with('success', 'カテゴリを削除しました');
}

// カテゴリ詳細（タスク一覧）
public function show(Category $category)
{
    $user = Auth::user();

    $hasAccess = $category->user_id === $user->id ||
        CategoryUserShare::where('category_id', $category->id)
            ->where('shared_user_id', $user->id)
            ->where('is_confirmed', true)
            ->exists();

    if (!$hasAccess) {
        abort(403);
    }

    $tasks = $category->tasks()->with(['category:id,name,icon_path'])
        ->orderBy('created_at', 'desc')
        ->get();
    $isOwner = $category->user_id === $user->id;
    
    $share = CategoryUserShare::where('category_id', $category->id)
        ->where('shared_user_id', $user->id)
        ->where('is_confirmed', true)
        ->first();

    $sharedPermission = $share->permission ?? null;
    $canEdit = $isOwner || in_array($sharedPermission, ['edit', 'full']);

    // 共有グループをメンバーと一緒に取得（N+1問題を解消）
    $groups = ShareGroup::with(['members.user'])
        ->where('user_id', $user->id)
        ->get();

    return view('categories.show', compact('category', 'tasks', 'isOwner', 'canEdit', 'groups', 'sharedPermission'));
}

// タスク追加
public function addTask(Request $request, Category $category)
{
    
    $user = Auth::user();

    $hasEditPermission = $category->sharedUsers()
        ->where('shared_user_id', $user->id)
        ->where('permission', 'full')
        ->where('is_confirmed', true)
        ->exists();

    if ($category->user_id !== $user->id && !$hasEditPermission) {
        abort(403);
    }

    $request->validate([
        'title' => 'required|string|max:255',
        'due_date' => 'nullable|date',
        'note' => 'nullable|string',
        'use_ai_advisor' => 'nullable|boolean',
    ]);

    $useAi = $request->boolean('use_ai_advisor');
    //$aiLimit = config('tastack.ai_limit', 3);
    $aiLimit = $user->ai_advisor_limit_per_day;
    $aiAdvice = null;
    $usedAi = false;
    
// AIアドバイザー利用可 & チェックされていた場合
if ($useAi && $user->ai_advisor_used_today < $aiLimit) {
    $prompt = "ユーザーはカテゴリ「{$category->name}」内で「{$request->title}」というタスクを作成しました。";

    if (!empty($request->note)) {
        $prompt .= "このタスクには以下の補足情報があります：「{$request->note}」。";
    }

    $prompt .= "全角200文字以内でアドバイスまたは提案して下さい。";

    try {
        $response = \Illuminate\Support\Facades\Http::withToken(env('OPENAI_API_KEY'))->post(
            'https://api.openai.com/v1/chat/completions',
            [
                'model' => 'gpt-4o',
                'messages' => [
                    ['role' => 'system', 'content' => 'あなたは優秀なAIアドバイザーです。'],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'max_tokens' => 200,
                'temperature' => 0.7,
            ]
        );

        $aiAdvice = $response->json('choices.0.message.content');
        $usedAi = true;

        // 使用回数加算
        $user->increment('ai_advisor_used_today');
        $user->ai_advisor_last_used_at = now();
        $user->save();
    } catch (\Exception $e) {
        \Log::error('AIアドバイス取得失敗: ' . $e->getMessage());
        $aiAdvice = '（AIアドバイスの取得に失敗しました）';
    }
}

    // タスク保存
    $task = $category->tasks()->create([
        'title' => $request->title,
        'due_date' => $request->due_date,
        'note' => $request->note,
        'used_ai_advisor' => $usedAi,
        'ai_advice' => $aiAdvice,
    ]);

    // キャッシュ無効化
    $this->invalidateUserCache($user->id);

    // Googleカレンダー追加（必要時）
    if (
        $user->google_token &&
        $task->due_date &&
        $request->has('add_to_google_calendar')
    ) {
        // 審査通過後に下記を有効化
         $this->addToGoogleCalendar($user, $task);
    }

    return redirect()->route('categories.show', $category)->with('success', 'タスクを追加しました');
}

/**
 * ユーザーのキャッシュを無効化
 */
private function invalidateUserCache($userId)
{
    \Cache::forget("dashboard_user_{$userId}");
    \Cache::forget("category_stats_user_{$userId}");
    \Cache::forget("user_stats_{$userId}");
    \Cache::forget("shared_categories_user_{$userId}");
}

// カテゴリ共有処理
public function share(Request $request, Category $category)
{
    if ($category->user_id !== Auth::id()) {
        abort(403);
    }

    // バリデーション（配列であることだけチェック。中身は後で）
    $request->validate([
        'identifiers' => 'required|array',
        'permissions' => 'required|array',
    ]);

    $errors = [];
    $sharedCount = 0;

    foreach ($request->identifiers as $index => $identifier) {
        $identifier = trim($identifier);

        // 空欄はスキップ
        if (empty($identifier)) {
            continue;
        }

        $permission = $request->permissions[$index] ?? 'view';

        $sharedUser = User::where('email', $identifier)
            ->orWhere('account_id', $identifier)
            ->first();

        if (!$sharedUser) {
            $errors[] = "【{$identifier}】は存在しないためスキップされました。";
            continue;
        }

        $token = (string) Str::uuid();

        $existing = CategoryUserShare::where('category_id', $category->id)
            ->where('shared_user_id', $sharedUser->id)
            ->first();

        if ($existing) {
            $existing->update([
                'permission' => $permission,
                'confirmation_token' => $token,
                'is_confirmed' => false,
            ]);
        } else {
            CategoryUserShare::create([
                'category_id' => $category->id,
                'shared_user_id' => $sharedUser->id,
                'permission' => $permission,
                'confirmation_token' => $token,
                'is_confirmed' => false,
            ]);
        }

        $sharedUser->notify(new CategoryShareInvitation($category, $token));
        $sharedCount++;
    }

    $msg = "{$sharedCount}件の共有リンクを送信しました。";
    if (!empty($errors)) {
        $msg .= ' エラー: ' . implode(' / ', $errors);
    }

    return redirect()->route('categories.show', $category)->with('success', $msg);
}

    public function updateShare(Request $request, Category $category, User $user)
    {
        if ($category->user_id !== Auth::id()) {
            return response()->json(['error' => '権限がありません'], 403);
        }

        $permission = $request->input('permission');

        if (!in_array($permission, ['view', 'edit', 'full'])) {
            return response()->json(['error' => '不正な権限です'], 422);
        }

        $category->sharedUsers()->updateExistingPivot($user->id, [
            'permission' => $permission,
        ]);

        return response()->json(['message' => '共有権限を更新しました']);
    }

    public function deleteShare(Category $category, User $user)
    {
        if ($category->user_id !== Auth::id()) {
            return response()->json(['error' => '権限がありません'], 403);
        }

        $category->sharedUsers()->detach($user->id);

        return response()->json(['message' => '共有を解除しました']);
    }

    // 共有確認処理（通知リンククリック後）    
public function confirmShare($token)
{
    $share = CategoryUserShare::where('confirmation_token', $token)->first();

    if (!$share) {
        abort(404);
    }

    if ($share->shared_user_id !== Auth::id()) {
        abort(403);
    }

    // is_confirmed のみを更新（tokenは残す）
    $share->update([
        'is_confirmed' => true,
    ]);

    return redirect()->route('shared.categories.show', ['token' => $token])
        ->with('success', '共有されたカテゴリにアクセスできるようになりました');
}

public function sharedTasks()
{
    $user = Auth::user();

    // N+1問題を解消：カテゴリ、タスク、共有情報を一度に取得
    $sharedCategories = $user->sharedCategories()
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

    return view('categories.shared', compact('sharedCategories'));
}

// Google審査完了後

/*
private function addToGoogleCalendar($user, $task)
{
    $client = new \Google_Client();
    $client->setClientId(config('services.google.client_id'));
    $client->setClientSecret(config('services.google.client_secret'));
    $client->setAccessToken([
        'access_token' => $user->google_token,
        'refresh_token' => $user->google_refresh_token,
    ]);
    $client->addScope(\Google_Service_Calendar::CALENDAR_EVENTS);

    if ($client->isAccessTokenExpired() && $client->getRefreshToken()) {
        $newToken = $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
        if (!empty($newToken['access_token'])) {
            $user->google_token = $newToken['access_token'];
            $user->save();
        }
    }

    $calendar = new \Google_Service_Calendar($client);

    $event = new \Google_Service_Calendar_Event([
        'summary' => $task->title,
        'description' => $task->note ?? '',
        'start' => [
            'dateTime' => \Carbon\Carbon::parse($task->due_date)->setTime(10, 0)->toRfc3339String(),
            'timeZone' => 'Asia/Tokyo',
        ],
        'end' => [
            'dateTime' => \Carbon\Carbon::parse($task->due_date)->setTime(11, 0)->toRfc3339String(),
            'timeZone' => 'Asia/Tokyo',
        ],
    ]);

    $calendar->events->insert('primary', $event);
}
*/

private function addToGoogleCalendar($user, $task)
{
    $client = new \Google_Client();
    $client->setClientId(config('services.google.client_id'));
    $client->setClientSecret(config('services.google.client_secret'));
    $client->setAccessToken([
        'access_token' => $user->google_token,
        'refresh_token' => $user->google_refresh_token,
    ]);
    $client->addScope(\Google_Service_Calendar::CALENDAR_EVENTS);

    if ($client->isAccessTokenExpired() && $client->getRefreshToken()) {
        $newToken = $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
        if (!empty($newToken['access_token'])) {
            $user->google_token = $newToken['access_token'];
            $user->save();
        }
    }

    $calendar = new \Google_Service_Calendar($client);

    $due = \Carbon\Carbon::parse($task->due_date)->timezone('Asia/Tokyo');

    // 00:00:00 の場合は終日イベントとして追加
    if ($due->format('H:i:s') === '00:00:00') {
        $event = new \Google_Service_Calendar_Event([
            'summary' => $task->title,
            'description' => $task->note ?? '',
            'colorId' => $user->google_calendar_color ?? '1',
            'start' => [
                'date' => $due->toDateString(), // 終日イベント用
                'timeZone' => 'Asia/Tokyo',
            ],
            'end' => [
                'date' => $due->copy()->addDay()->toDateString(), // 翌日を終了日とする
                'timeZone' => 'Asia/Tokyo',
            ],
        ]);
    } else {
        // 時間ありイベント
        $event = new \Google_Service_Calendar_Event([
            'summary' => $task->title,
            'description' => $task->note ?? '',
            'colorId' => $user->google_calendar_color ?? '1',
            'start' => [
                'dateTime' => $due->toRfc3339String(),
                'timeZone' => 'Asia/Tokyo',
            ],
            'end' => [
                'dateTime' => $due->copy()->addHour()->toRfc3339String(),
                'timeZone' => 'Asia/Tokyo',
            ],
        ]);
    }

    $calendar->events->insert('primary', $event);
}
    
}
