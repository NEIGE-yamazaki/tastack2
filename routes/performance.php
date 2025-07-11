<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaginatedController;
use App\Http\Controllers\CachedController;

Route::middleware(['auth', 'verified'])->group(function () {
    
    // ページネーション機能
    Route::get('/categories/paginated', [PaginatedController::class, 'paginatedCategories'])->name('categories.paginated');
    Route::get('/categories/{category}/tasks/paginated', [PaginatedController::class, 'paginatedTasks'])->name('tasks.paginated');
    Route::get('/shared-categories/paginated', [PaginatedController::class, 'paginatedSharedCategories'])->name('shared-categories.paginated');
    
    // 無限スクロール・仮想スクロール
    Route::get('/categories/{category}/tasks/infinite-scroll', [PaginatedController::class, 'infiniteScrollTasks'])->name('tasks.infinite-scroll');
    Route::get('/categories/{category}/tasks/virtual-scroll', [PaginatedController::class, 'virtualScrollTasks'])->name('tasks.virtual-scroll');
    
    // キャッシュ機能
    Route::get('/dashboard/cached', [CachedController::class, 'cachedDashboard'])->name('dashboard.cached');
    Route::get('/api/category-stats', [CachedController::class, 'cachedCategoryStats'])->name('api.category-stats');
    Route::get('/api/user-stats', [CachedController::class, 'cachedUserStats'])->name('api.user-stats');
    Route::get('/api/shared-categories', [CachedController::class, 'cachedSharedCategories'])->name('api.shared-categories');
    Route::get('/api/popular-categories', [CachedController::class, 'cachedPopularCategories'])->name('api.popular-categories');
    Route::delete('/api/cache', [CachedController::class, 'clearCache'])->name('api.cache.clear');
    
});
