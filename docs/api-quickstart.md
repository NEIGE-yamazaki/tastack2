# TASTACK2 API クイックスタートガイド

## 🚀 はじめに

このガイドでは、TASTACK2 APIを使用してアプリケーションを開発するための基本的な手順を説明します。

## 📋 前提条件

- PHP 8.1以上
- Composer
- Node.js 18以上
- Docker Desktop (オプション)

## 🔧 セットアップ

### 1. プロジェクトクローン

```bash
git clone https://github.com/your-org/tastack2.git
cd tastack2
```

### 2. 依存関係のインストール

```bash
# PHP依存関係
composer install

# Node.js依存関係
npm install
```

### 3. 環境設定

```bash
# 環境設定ファイルをコピー
cp .env.example .env

# アプリケーションキーを生成
php artisan key:generate
```

### 4. データベースセットアップ

```bash
# マイグレーション実行
php artisan migrate

# シーダー実行（オプション）
php artisan db:seed
```

### 5. 開発サーバー起動

```bash
# Laravel開発サーバー
php artisan serve

# Vite開発サーバー（別ターミナル）
npm run dev
```

## 📡 基本的なAPI使用方法

### 認証

#### 1. ユーザー登録

```bash
curl -X POST http://localhost:8000/register \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "name": "テストユーザー",
    "email": "test@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'
```

#### 2. ログイン

```bash
curl -X POST http://localhost:8000/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -c cookies.txt \
  -d '{
    "email": "test@example.com",
    "password": "password123"
  }'
```

### カテゴリ操作

#### 1. カテゴリ作成

```bash
curl -X POST http://localhost:8000/categories \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "X-CSRF-TOKEN: YOUR_CSRF_TOKEN" \
  -b cookies.txt \
  -d '{
    "name": "仕事"
  }'
```

#### 2. カテゴリ一覧取得

```bash
curl -X GET http://localhost:8000/categories \
  -H "Accept: application/json" \
  -b cookies.txt
```

#### 3. カテゴリ詳細取得

```bash
curl -X GET http://localhost:8000/categories/1 \
  -H "Accept: application/json" \
  -b cookies.txt
```

### タスク操作

#### 1. タスク作成

```bash
curl -X POST http://localhost:8000/categories/1/tasks \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "X-CSRF-TOKEN: YOUR_CSRF_TOKEN" \
  -b cookies.txt \
  -d '{
    "title": "プレゼン資料作成",
    "due_date": "2025-07-15 10:00:00",
    "note": "月末までに完成させる",
    "use_ai_advisor": true
  }'
```

#### 2. タスク完了状態切り替え

```bash
curl -X PATCH http://localhost:8000/tasks/1/toggle \
  -H "Accept: application/json" \
  -H "X-CSRF-TOKEN: YOUR_CSRF_TOKEN" \
  -b cookies.txt
```

## 🔐 認証の詳細

### CSRFトークン取得

```bash
curl -X GET http://localhost:8000/sanctum/csrf-cookie \
  -c cookies.txt
```

### セッション認証

TASTACK2では、Web アプリケーション向けにLaravel Sanctumによるセッション認証を使用しています。

1. `/sanctum/csrf-cookie` でCSRFトークンを取得
2. ログインしてセッションを確立
3. 後続のリクエストでセッションクッキーとCSRFトークンを送信

## 📄 JavaScript例

### Fetch API を使用した例

```javascript
// CSRF トークン取得
async function getCsrfToken() {
    await fetch('/sanctum/csrf-cookie', {
        credentials: 'same-origin'
    });
    return document.querySelector('meta[name="csrf-token"]').getAttribute('content');
}

// ログイン
async function login(email, password) {
    const csrfToken = await getCsrfToken();
    
    const response = await fetch('/login', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        credentials: 'same-origin',
        body: JSON.stringify({
            email: email,
            password: password
        })
    });
    
    return response.json();
}

// カテゴリ作成
async function createCategory(name) {
    const csrfToken = await getCsrfToken();
    
    const response = await fetch('/categories', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        credentials: 'same-origin',
        body: JSON.stringify({
            name: name
        })
    });
    
    return response.json();
}

// タスク作成
async function createTask(categoryId, title, dueDate = null, note = null) {
    const csrfToken = await getCsrfToken();
    
    const response = await fetch(`/categories/${categoryId}/tasks`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        credentials: 'same-origin',
        body: JSON.stringify({
            title: title,
            due_date: dueDate,
            note: note,
            use_ai_advisor: true
        })
    });
    
    return response.json();
}
```

## 🔄 Alpine.js との統合

```javascript
// Alpine.js コンポーネント例
document.addEventListener('alpine:init', () => {
    Alpine.data('taskManager', () => ({
        categories: [],
        tasks: [],
        currentCategory: null,
        
        async init() {
            await this.loadCategories();
        },
        
        async loadCategories() {
            try {
                const response = await fetch('/categories', {
                    headers: {
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin'
                });
                
                if (response.ok) {
                    const data = await response.json();
                    this.categories = data.data;
                }
            } catch (error) {
                console.error('カテゴリ読み込みエラー:', error);
            }
        },
        
        async loadTasks(categoryId) {
            try {
                const response = await fetch(`/categories/${categoryId}`, {
                    headers: {
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin'
                });
                
                if (response.ok) {
                    const data = await response.json();
                    this.tasks = data.tasks;
                    this.currentCategory = data.category;
                }
            } catch (error) {
                console.error('タスク読み込みエラー:', error);
            }
        },
        
        async toggleTask(taskId) {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            try {
                const response = await fetch(`/tasks/${taskId}/toggle`, {
                    method: 'PATCH',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    credentials: 'same-origin'
                });
                
                if (response.ok) {
                    const data = await response.json();
                    // UI更新
                    const task = this.tasks.find(t => t.id === taskId);
                    if (task) {
                        task.is_done = data.is_done;
                    }
                }
            } catch (error) {
                console.error('タスク切り替えエラー:', error);
            }
        }
    }));
});
```

## 📱 Capacitor.js との統合

```javascript
// Capacitor プラグイン設定
import { CapacitorConfig } from '@capacitor/cli';

const config: CapacitorConfig = {
    appId: 'com.hintoru.tastack2',
    appName: 'TASTACK2',
    webDir: 'public',
    server: {
        url: 'http://localhost:8000',
        cleartext: true
    },
    plugins: {
        SplashScreen: {
            launchShowDuration: 3000,
            launchAutoHide: true
        }
    }
};

export default config;
```

```javascript
// モバイルAPI使用例
import { Capacitor } from '@capacitor/core';
import { Camera, CameraResultType, CameraSource } from '@capacitor/camera';
import { Geolocation } from '@capacitor/geolocation';

// デバイス情報取得
async function getDeviceInfo() {
    return {
        platform: Capacitor.getPlatform(),
        isNative: Capacitor.isNativePlatform(),
        isWeb: !Capacitor.isNativePlatform()
    };
}

// カメラ使用
async function takePicture() {
    try {
        const image = await Camera.getPhoto({
            quality: 90,
            allowEditing: true,
            resultType: CameraResultType.DataUrl,
            source: CameraSource.Camera
        });
        
        return image.dataUrl;
    } catch (error) {
        console.error('カメラエラー:', error);
        throw error;
    }
}

// 位置情報取得
async function getCurrentLocation() {
    try {
        const coordinates = await Geolocation.getCurrentPosition();
        return {
            latitude: coordinates.coords.latitude,
            longitude: coordinates.coords.longitude
        };
    } catch (error) {
        console.error('位置情報エラー:', error);
        throw error;
    }
}
```

## 🛠️ 開発者向けツール

### 1. API テスト

```bash
# PHPUnit テスト実行
php artisan test

# 特定のテストクラス実行
php artisan test --filter=CategoryTest
```

### 2. コード品質チェック

```bash
# PHP CS Fixer
vendor/bin/php-cs-fixer fix

# PHPStan
vendor/bin/phpstan analyse
```

### 3. デバッグ

```bash
# ログファイル監視
tail -f storage/logs/laravel.log

# デバッグモード有効化
php artisan config:cache
```

## 🌟 ベストプラクティス

### 1. エラーハンドリング

```javascript
async function apiCall(url, options = {}) {
    try {
        const response = await fetch(url, {
            headers: {
                'Accept': 'application/json',
                ...options.headers
            },
            credentials: 'same-origin',
            ...options
        });
        
        if (!response.ok) {
            const error = await response.json();
            throw new Error(error.message || 'APIエラーが発生しました');
        }
        
        return await response.json();
    } catch (error) {
        console.error('API呼び出しエラー:', error);
        throw error;
    }
}
```

### 2. 認証状態管理

```javascript
// 認証状態チェック
async function checkAuthStatus() {
    try {
        const response = await fetch('/api/user', {
            headers: {
                'Accept': 'application/json'
            },
            credentials: 'same-origin'
        });
        
        return response.ok;
    } catch (error) {
        return false;
    }
}
```

### 3. データキャッシュ

```javascript
// シンプルなメモリキャッシュ
class ApiCache {
    constructor() {
        this.cache = new Map();
        this.ttl = 5 * 60 * 1000; // 5分
    }
    
    get(key) {
        const item = this.cache.get(key);
        if (item && Date.now() - item.timestamp < this.ttl) {
            return item.data;
        }
        this.cache.delete(key);
        return null;
    }
    
    set(key, data) {
        this.cache.set(key, {
            data: data,
            timestamp: Date.now()
        });
    }
}

const apiCache = new ApiCache();
```

## 🐛 トラブルシューティング

### よくある問題と解決方法

1. **CSRF トークンエラー**
   - CSRFトークンが取得できているか確認
   - メタタグが正しく設定されているか確認

2. **CORS エラー**
   - 同一オリジンからのリクエストか確認
   - `credentials: 'same-origin'` が設定されているか確認

3. **認証エラー**
   - セッションが有効か確認
   - ログイン状態を確認

4. **バリデーションエラー**
   - リクエストボディが正しい形式か確認
   - 必須フィールドが含まれているか確認

## 📚 参考資料

- [Laravel公式ドキュメント](https://laravel.com/docs)
- [Capacitor公式ドキュメント](https://capacitorjs.com/docs)
- [Alpine.js公式ドキュメント](https://alpinejs.dev)
- [TASTACK2 API仕様書](./api-specification.md)

## 🤝 サポート

- **GitHub Issues**: [プロジェクトページ](https://github.com/your-org/tastack2/issues)
- **Discord**: [開発者コミュニティ](https://discord.gg/tastack2)
- **メール**: support@tastack2.com

---

最終更新: 2025年7月11日
