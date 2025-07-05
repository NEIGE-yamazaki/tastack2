<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ShareConfirmationController;
use App\Http\Controllers\SharedCategoryController;
use App\Http\Controllers\Auth\SocialiteController;
use App\Http\Controllers\DashboardController;

// トップページ
Route::get('/', function () {
    return view('home');
});

// モバイル機能テストページ
Route::get('/mobile-test', function () {
    return view('mobile-test');
})->name('mobile-test');

// 運営会社
Route::view('/company', 'static.company')->name('company');
// 利用規約
Route::view('/termsofservice', 'static.termsofservice')->name('termsofservice');
// プライバシーポリシー
Route::view('/privacypolicy', 'static.privacypolicy')->name('privacypolicy');


// ダッシュボード（認証＋メール確認）
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth'])
    ->name('dashboard');


// ----------------------
// 認証が必要なルート群
// ----------------------
Route::middleware('auth')->group(function () {

    // プロフィール関連
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // カテゴリ一覧・作成
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::patch('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy'); // ←これを追加
    Route::post('/categories/reorder', [CategoryController::class, 'reorder'])->name('categories.reorder');

    // カテゴリ詳細（タスク一覧）
    Route::get('/categories/{category}', [CategoryController::class, 'show'])->name('categories.show');

    // カテゴリ共有リンク送信
    Route::post('/categories/{category}/share', [CategoryController::class, 'share'])->name('categories.share');

    // 共有リンク確認（トークンから共有を承認）
    Route::get('/categories/share/confirm/{token}', [CategoryController::class, 'confirmShare'])->name('categories.share.confirm');

    // タスク操作
    Route::post('/categories/{category}/tasks', [CategoryController::class, 'addTask'])->name('categories.tasks.store');
    Route::patch('/tasks/{task}/toggle', [TaskController::class, 'toggle'])->name('tasks.toggle');
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');
    Route::patch('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');

    // 共有タスク
    Route::get('/shared-tasks', [CategoryController::class, 'sharedTasks'])->name('categories.shared');
    // 共有情報の更新（権限変更）
    Route::patch('/categories/{category}/share/{user}', [CategoryController::class, 'updateShare'])->name('categories.share.update');
    // 共有情報の削除（共有解除）
    Route::delete('/categories/{category}/share/{user}', [CategoryController::class, 'deleteShare'])->name('categories.share.delete');

    // 共有グループ
    Route::get('/share-groups', [\App\Http\Controllers\ShareGroupController::class, 'index'])->name('share-groups.index');
    Route::post('/share-groups', [\App\Http\Controllers\ShareGroupController::class, 'store'])->name('share-groups.store');
    Route::get('/share-groups/{group}/edit', [\App\Http\Controllers\ShareGroupController::class, 'edit'])->name('share-groups.edit'); // ← 追加
    Route::patch('/share-groups/{group}', [\App\Http\Controllers\ShareGroupController::class, 'update'])->name('share-groups.update'); // ← 追加
    Route::delete('/share-groups/{group}', [\App\Http\Controllers\ShareGroupController::class, 'destroy'])->name('share-groups.destroy');

    // 閲覧専用：共有カテゴリ表示
    Route::get('/shared/{token}', [SharedCategoryController::class, 'show'])
        ->middleware(['auth'])
        ->name('shared.categories.show');

});


// 「署名付き」URL用（メール内リンク） ※ログイン後にアクセス
Route::get('/shared/confirm', [ShareConfirmationController::class, 'confirm'])
    ->middleware(['signed', 'auth'])
    ->name('shared.confirm');


// SNS
Route::get('/login/{provider}', [SocialiteController::class, 'redirectToProvider'])->name('login.provider');
Route::get('/login/{provider}/callback', [SocialiteController::class, 'handleProviderCallback']);


require __DIR__ . '/auth.php';
