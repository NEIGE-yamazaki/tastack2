# TASTACK2 開発者ガイド

## 🚀 概要

TASTACK2は、Laravel 10 + Capacitor.jsで構築された、クロスプラットフォーム対応のタスク管理システムです。
このガイドでは、APIの使用方法、開発のベストプラクティス、よくある問題の解決方法について説明します。

## 📋 目次

1. [セットアップ](#セットアップ)
2. [認証システム](#認証システム)
3. [API使用方法](#api使用方法)
4. [実装例](#実装例)
5. [トラブルシューティング](#トラブルシューティング)
6. [ベストプラクティス](#ベストプラクティス)

## 🔧 セットアップ

### 必要な環境

- PHP 8.1以上
- Composer
- Node.js 18以上
- Docker Desktop（オプション）

### プロジェクトセットアップ

```bash
# リポジトリをクローン
git clone https://github.com/your-org/tastack2.git
cd tastack2

# 依存関係のインストール
composer install
npm install

# 環境設定
cp .env.example .env
php artisan key:generate

# データベースセットアップ
php artisan migrate
php artisan db:seed

# 開発サーバー起動
php artisan serve
npm run dev
```

## 🔐 認証システム

### Laravel Sanctum

TASTACK2は、Laravel Sanctumを使用してセッションベースの認証を実装しています。

#### CSRFトークンの取得

```javascript
// HTMLメタタグから取得
const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

// APIリクエストで使用
fetch('/api/endpoint', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'X-CSRF-TOKEN': token
  },
  body: JSON.stringify(data)
});
```

#### 認証状態の確認

```javascript
// 認証状態をチェック
async function checkAuth() {
  try {
    const response = await fetch('/dashboard', {
      headers: {
        'Accept': 'application/json'
      }
    });
    return response.ok;
  } catch (error) {
    return false;
  }
}
```

## 📡 API使用方法

### 基本的なHTTPリクエスト

#### GET リクエスト

```javascript
// カテゴリ一覧取得
async function getCategories() {
  const response = await fetch('/categories', {
    headers: {
      'Accept': 'application/json'
    }
  });
  
  if (!response.ok) {
    throw new Error('カテゴリの取得に失敗しました');
  }
  
  return await response.json();
}
```

#### POST リクエスト

```javascript
// カテゴリ作成
async function createCategory(data) {
  const response = await fetch('/categories', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'X-CSRF-TOKEN': getCSRFToken()
    },
    body: JSON.stringify(data)
  });
  
  if (!response.ok) {
    const error = await response.json();
    throw new Error(error.message || 'カテゴリの作成に失敗しました');
  }
  
  return await response.json();
}
```

#### PATCH リクエスト

```javascript
// タスク更新
async function updateTask(taskId, data) {
  const response = await fetch(`/tasks/${taskId}`, {
    method: 'PATCH',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'X-CSRF-TOKEN': getCSRFToken()
    },
    body: JSON.stringify(data)
  });
  
  return await response.json();
}
```

#### DELETE リクエスト

```javascript
// タスク削除
async function deleteTask(taskId) {
  const response = await fetch(`/tasks/${taskId}`, {
    method: 'DELETE',
    headers: {
      'Accept': 'application/json',
      'X-CSRF-TOKEN': getCSRFToken()
    }
  });
  
  return response.ok;
}
```

### エラーハンドリング

```javascript
// 統一されたエラーハンドリング
async function apiRequest(url, options = {}) {
  try {
    const response = await fetch(url, {
      ...options,
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': getCSRFToken(),
        ...options.headers
      }
    });
    
    if (!response.ok) {
      const error = await response.json();
      throw new Error(error.message || `HTTP ${response.status}`);
    }
    
    return await response.json();
  } catch (error) {
    console.error('API Request Error:', error);
    throw error;
  }
}
```

## 💡 実装例

### カテゴリ管理

```javascript
class CategoryManager {
  constructor() {
    this.categories = [];
  }
  
  // カテゴリ一覧取得
  async loadCategories() {
    try {
      const response = await apiRequest('/categories');
      this.categories = response.categories;
      return this.categories;
    } catch (error) {
      console.error('カテゴリの読み込みに失敗:', error);
      throw error;
    }
  }
  
  // カテゴリ作成
  async createCategory(name, icon = null) {
    try {
      const formData = new FormData();
      formData.append('name', name);
      if (icon) {
        formData.append('icon', icon);
      }
      
      const response = await fetch('/categories', {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': getCSRFToken()
        },
        body: formData
      });
      
      if (!response.ok) {
        throw new Error('カテゴリの作成に失敗しました');
      }
      
      const result = await response.json();
      this.categories.push(result.category);
      return result.category;
    } catch (error) {
      console.error('カテゴリの作成に失敗:', error);
      throw error;
    }
  }
  
  // カテゴリ並び替え
  async reorderCategories(orders) {
    try {
      const response = await apiRequest('/categories/reorder', {
        method: 'POST',
        body: JSON.stringify({ orders })
      });
      
      return response;
    } catch (error) {
      console.error('カテゴリの並び替えに失敗:', error);
      throw error;
    }
  }
}
```

### タスク管理

```javascript
class TaskManager {
  constructor() {
    this.tasks = [];
  }
  
  // タスク作成
  async createTask(categoryId, taskData) {
    try {
      const response = await apiRequest(`/categories/${categoryId}/tasks`, {
        method: 'POST',
        body: JSON.stringify(taskData)
      });
      
      return response.task;
    } catch (error) {
      console.error('タスクの作成に失敗:', error);
      throw error;
    }
  }
  
  // タスク完了切り替え
  async toggleTask(taskId) {
    try {
      const response = await apiRequest(`/tasks/${taskId}/toggle`, {
        method: 'PATCH'
      });
      
      return response.task;
    } catch (error) {
      console.error('タスクの切り替えに失敗:', error);
      throw error;
    }
  }
  
  // タスク一括操作
  async bulkUpdateTasks(tasks) {
    const promises = tasks.map(task => 
      this.updateTask(task.id, task.data)
    );
    
    try {
      const results = await Promise.all(promises);
      return results;
    } catch (error) {
      console.error('タスクの一括更新に失敗:', error);
      throw error;
    }
  }
}
```

### 共有機能

```javascript
class ShareManager {
  // カテゴリ共有
  async shareCategory(categoryId, email, permission = 'view') {
    try {
      const response = await apiRequest(`/categories/${categoryId}/share`, {
        method: 'POST',
        body: JSON.stringify({
          email,
          permission,
          message: 'カテゴリを共有します'
        })
      });
      
      return response;
    } catch (error) {
      console.error('カテゴリの共有に失敗:', error);
      throw error;
    }
  }
  
  // 共有タスク一覧取得
  async getSharedTasks() {
    try {
      const response = await apiRequest('/shared-tasks');
      return response.shared_categories;
    } catch (error) {
      console.error('共有タスクの取得に失敗:', error);
      throw error;
    }
  }
}
```

## 🔍 トラブルシューティング

### よくある問題と解決方法

#### 1. CSRFトークンエラー

```javascript
// 問題: 419 CSRF token mismatch
// 解決方法: トークンを正しく送信する
function getCSRFToken() {
  const token = document.querySelector('meta[name="csrf-token"]');
  if (!token) {
    console.error('CSRF token not found');
    return '';
  }
  return token.getAttribute('content');
}
```

#### 2. 認証エラー

```javascript
// 問題: 401 Unauthorized
// 解決方法: 認証状態を確認し、必要に応じて再ログイン
async function handleAuthError() {
  const isAuthenticated = await checkAuth();
  if (!isAuthenticated) {
    window.location.href = '/login';
  }
}
```

#### 3. バリデーションエラー

```javascript
// 問題: 422 Unprocessable Entity
// 解決方法: エラーメッセージを表示し、ユーザーに修正を促す
function displayValidationErrors(errors) {
  Object.keys(errors).forEach(field => {
    const errorElement = document.querySelector(`#${field}-error`);
    if (errorElement) {
      errorElement.textContent = errors[field][0];
      errorElement.style.display = 'block';
    }
  });
}
```

#### 4. ファイルアップロードエラー

```javascript
// 問題: ファイルサイズ制限やフォーマットエラー
// 解決方法: 事前にファイルを検証する
function validateFile(file) {
  const maxSize = 10 * 1024 * 1024; // 10MB
  const allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
  
  if (file.size > maxSize) {
    throw new Error('ファイルサイズが大きすぎます（最大10MB）');
  }
  
  if (!allowedTypes.includes(file.type)) {
    throw new Error('対応していないファイル形式です');
  }
  
  return true;
}
```

## 🎯 ベストプラクティス

### 1. エラーハンドリング

```javascript
// 統一されたエラーハンドリング
class APIError extends Error {
  constructor(message, status, errors = {}) {
    super(message);
    this.name = 'APIError';
    this.status = status;
    this.errors = errors;
  }
}

async function handleAPIResponse(response) {
  if (!response.ok) {
    const error = await response.json().catch(() => ({}));
    throw new APIError(
      error.message || '予期しないエラーが発生しました',
      response.status,
      error.errors || {}
    );
  }
  
  return await response.json();
}
```

### 2. ローディング状態の管理

```javascript
class LoadingManager {
  constructor() {
    this.loadingStates = new Map();
  }
  
  setLoading(key, isLoading) {
    this.loadingStates.set(key, isLoading);
    this.updateUI();
  }
  
  isLoading(key) {
    return this.loadingStates.get(key) || false;
  }
  
  updateUI() {
    // ローディングUIの更新
    const hasLoading = Array.from(this.loadingStates.values()).some(Boolean);
    const spinner = document.querySelector('.loading-spinner');
    if (spinner) {
      spinner.style.display = hasLoading ? 'block' : 'none';
    }
  }
}
```

### 3. キャッシュ管理

```javascript
class CacheManager {
  constructor() {
    this.cache = new Map();
    this.cacheTimeout = 5 * 60 * 1000; // 5分
  }
  
  set(key, data) {
    this.cache.set(key, {
      data,
      timestamp: Date.now()
    });
  }
  
  get(key) {
    const cached = this.cache.get(key);
    if (!cached) return null;
    
    if (Date.now() - cached.timestamp > this.cacheTimeout) {
      this.cache.delete(key);
      return null;
    }
    
    return cached.data;
  }
  
  clear() {
    this.cache.clear();
  }
}
```

### 4. レート制限への対応

```javascript
class RateLimiter {
  constructor() {
    this.requests = new Map();
    this.limits = {
      auth: { max: 5, window: 60000 }, // 5回/分
      api: { max: 60, window: 60000 }, // 60回/分
      search: { max: 30, window: 60000 } // 30回/分
    };
  }
  
  canMakeRequest(category) {
    const now = Date.now();
    const limit = this.limits[category];
    
    if (!limit) return true;
    
    const requests = this.requests.get(category) || [];
    const validRequests = requests.filter(
      time => now - time < limit.window
    );
    
    this.requests.set(category, validRequests);
    
    return validRequests.length < limit.max;
  }
  
  recordRequest(category) {
    const requests = this.requests.get(category) || [];
    requests.push(Date.now());
    this.requests.set(category, requests);
  }
}
```

## 🔗 開発リソース

### 公式ドキュメント

- [Laravel Documentation](https://laravel.com/docs)
- [Capacitor Documentation](https://capacitorjs.com/docs)
- [API仕様書](./api-guide.md)
- [OpenAPI仕様](./openapi.yaml)

### ツール

- [Postman Collection](./TASTACK2_API.postman_collection.json)
- [API クイックスタート](./api-quickstart.md)

### 開発環境

```bash
# 開発サーバー起動
php artisan serve

# フロントエンドビルド（監視モード）
npm run dev

# テスト実行
php artisan test

# モバイルアプリビルド
npm run build:mobile
npx cap sync
```

## 📞 サポート

問題が発生した場合は、以下のリソースを確認してください：

1. [GitHub Issues](https://github.com/your-org/tastack2/issues)
2. [開発者向けFAQ](./api-guide.md#よくある質問)
3. [プロジェクトWiki](https://github.com/your-org/tastack2/wiki)

---

*このガイドは継続的に更新されます。最新の情報は [GitHubリポジトリ](https://github.com/your-org/tastack2) を確認してください。*
