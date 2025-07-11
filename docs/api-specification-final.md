# TASTACK2 API仕様書 - 完全版

## 🎯 概要

TASTACK2は、Laravel + Capacitor.jsベースのクロスプラットフォーム対応タスク管理システムです。
本仕様書では、Webアプリケーションおよびモバイルアプリケーションで使用される全APIエンドポイントについて詳細に記述します。

## 📊 システム情報

- **バックエンド**: Laravel 10
- **フロントエンド**: Vite + JavaScript
- **モバイル**: Capacitor.js
- **認証**: Laravel Sanctum
- **データベース**: MySQL/PostgreSQL
- **ストレージ**: Local/S3互換

## 🌐 ベースURL

| 環境 | URL | 説明 |
|------|-----|------|
| 開発環境 | `http://localhost:8081` | ローカル開発用 |
| ステージング | `https://staging.tastack2.com` | テスト環境 |
| 本番環境 | `https://api.tastack2.com` | 本番環境 |

## 🔐 認証

### 認証方式

- **Web**: Laravel Sanctumによるセッション認証
- **API**: Bearerトークン認証（将来実装予定）
- **モバイル**: セッション認証（WebViewベース）

### 必要なヘッダー

```http
Content-Type: application/json
Accept: application/json
X-CSRF-TOKEN: {csrf_token}
Authorization: Bearer {token}  # API認証時
```

### CSRF保護

すべてのPOST/PUT/PATCH/DELETEリクエストにはCSRFトークンが必要です。

```javascript
// CSRFトークンの取得
const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

// リクエストヘッダーに含める
fetch('/api/endpoint', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': token
    },
    body: JSON.stringify(data)
});
```

## 📋 エラーレスポンス

### 共通エラーフォーマット

```json
{
  "error": "エラーメッセージ",
  "message": "詳細なエラー説明",
  "status": 400,
  "errors": {
    "field_name": ["フィールド固有のエラー"]
  }
}
```

### HTTPステータスコード

| コード | 説明 | 用途 |
|--------|------|------|
| 200 | OK | 成功 |
| 201 | Created | 作成成功 |
| 204 | No Content | 削除成功 |
| 400 | Bad Request | リクエストエラー |
| 401 | Unauthorized | 認証エラー |
| 403 | Forbidden | 権限エラー |
| 404 | Not Found | リソースが見つからない |
| 422 | Unprocessable Entity | バリデーションエラー |
| 429 | Too Many Requests | レート制限 |
| 500 | Internal Server Error | サーバーエラー |

---

## 📁 1. 認証API

### 1.1 ログイン

```http
POST /login
```

#### リクエストパラメータ

```json
{
  "email": "user@example.com",
  "password": "password123",
  "remember": true
}
```

#### バリデーション

- `email`: 必須、メール形式
- `password`: 必須、8文字以上
- `remember`: オプション、boolean

#### 成功レスポンス (200)

```json
{
  "success": true,
  "redirect": "/dashboard",
  "user": {
    "id": 1,
    "name": "ユーザー名",
    "email": "user@example.com",
    "email_verified_at": "2025-01-01T00:00:00.000000Z"
  }
}
```

#### エラーレスポンス (422)

```json
{
  "error": "The given data was invalid.",
  "errors": {
    "email": ["メールアドレスが正しくありません"]
  }
}
```

### 1.2 ログアウト

```http
POST /logout
```

#### 成功レスポンス (200)

```json
{
  "success": true,
  "redirect": "/"
}
```

### 1.3 ユーザー登録

```http
POST /register
```

#### リクエストパラメータ

```json
{
  "name": "ユーザー名",
  "email": "user@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

#### バリデーション規則

- `name`: 必須、255文字以内
- `email`: 必須、メール形式、一意
- `password`: 必須、8文字以上
- `password_confirmation`: 必須、passwordと一致

#### 成功レスポンス (201)

```json
{
  "success": true,
  "redirect": "/email/verify",
  "message": "確認メールを送信しました"
}
```

### 1.4 SNSログイン

```http
GET /login/{provider}
```

#### パス パラメータ

- `provider`: `google` (現在はGoogleのみ対応)

#### レスポンス内容

リダイレクト先: プロバイダー認証ページ

---

## 🏠 2. ダッシュボードAPI

### 2.1 ダッシュボード情報取得

```http
GET /dashboard
```

#### 成功レスポンス (200)

```json
{
  "ownCategories": [
    {
      "id": 1,
      "name": "仕事",
      "display_order": 1,
      "icon_path": "/storage/icons/work.png",
      "incomplete_tasks_count": 3,
      "created_at": "2025-01-01T00:00:00.000000Z",
      "updated_at": "2025-01-01T00:00:00.000000Z"
    }
  ],
  "sharedCategories": [
    {
      "id": 2,
      "name": "チーム共有",
      "incomplete_tasks_count": 1,
      "pivot": {
        "permission": "edit",
        "created_at": "2025-01-01T00:00:00.000000Z"
      }
    }
  ],
  "stats": {
    "total_categories": 5,
    "total_tasks": 12,
    "completed_tasks": 8,
    "pending_tasks": 4
  }
}
```

---

## 📂 3. カテゴリAPI

### 3.1 カテゴリ一覧取得

```http
GET /categories
```

#### クエリパラメータ

- `sort`: 並び順 (`display_order`, `created_at`, `name`)
- `order`: 昇順/降順 (`asc`, `desc`)
- `with_tasks`: タスクを含めるか (`true`, `false`)

#### 成功レスポンス (200)

```json
{
  "categories": [
    {
      "id": 1,
      "name": "仕事",
      "display_order": 1,
      "icon_path": "/storage/icons/work.png",
      "incomplete_tasks_count": 3,
      "created_at": "2025-01-01T00:00:00.000000Z",
      "updated_at": "2025-01-01T00:00:00.000000Z",
      "tasks": [
        {
          "id": 1,
          "title": "会議準備",
          "is_done": false,
          "created_at": "2025-01-01T00:00:00.000000Z"
        }
      ]
    }
  ]
}
```

### 3.2 カテゴリ作成

```http
POST /categories
```

#### リクエストパラメータ

```json
{
  "name": "新しいカテゴリ",
  "icon": "file_object_or_path",
  "display_order": 1
}
```

#### バリデーション規則

- `name`: 必須、255文字以内
- `icon`: オプション、画像ファイル（jpg, png, gif）
- `display_order`: オプション、整数

#### 成功レスポンス (201)

```json
{
  "success": true,
  "category": {
    "id": 1,
    "name": "新しいカテゴリ",
    "display_order": 1,
    "icon_path": "/storage/icons/category_1.png",
    "created_at": "2025-01-01T00:00:00.000000Z",
    "updated_at": "2025-01-01T00:00:00.000000Z"
  },
  "message": "カテゴリを作成しました"
}
```

### 3.3 カテゴリ詳細取得

```http
GET /categories/{category}
```

#### パスパラメータ

- `category`: カテゴリID

#### 成功レスポンス (200)

```json
{
  "category": {
    "id": 1,
    "name": "仕事",
    "display_order": 1,
    "icon_path": "/storage/icons/work.png",
    "created_at": "2025-01-01T00:00:00.000000Z",
    "updated_at": "2025-01-01T00:00:00.000000Z",
    "tasks": [
      {
        "id": 1,
        "title": "会議準備",
        "description": "資料作成",
        "is_done": false,
        "due_date": "2025-01-15T00:00:00.000000Z",
        "created_at": "2025-01-01T00:00:00.000000Z",
        "updated_at": "2025-01-01T00:00:00.000000Z"
      }
    ],
    "shared_users": [
      {
        "id": 2,
        "name": "共有ユーザー",
        "email": "shared@example.com",
        "pivot": {
          "permission": "edit",
          "created_at": "2025-01-01T00:00:00.000000Z"
        }
      }
    ]
  }
}
```

### 3.4 カテゴリ更新

```http
PATCH /categories/{category}
```

#### リクエストパラメータ

```json
{
  "name": "更新されたカテゴリ名",
  "icon": "file_object_or_path",
  "display_order": 2
}
```

#### 成功レスポンス (200)

```json
{
  "success": true,
  "category": {
    "id": 1,
    "name": "更新されたカテゴリ名",
    "display_order": 2,
    "icon_path": "/storage/icons/updated.png",
    "updated_at": "2025-01-01T12:00:00.000000Z"
  },
  "message": "カテゴリを更新しました"
}
```

### 3.5 カテゴリ削除

```http
DELETE /categories/{category}
```

#### 成功レスポンス (204)

```json
{
  "success": true,
  "message": "カテゴリを削除しました"
}
```

### 3.6 カテゴリ並び替え

```http
POST /categories/reorder
```

#### リクエストパラメータ

```json
{
  "orders": [
    {"id": 1, "order": 1},
    {"id": 2, "order": 2},
    {"id": 3, "order": 3}
  ]
}
```

#### 成功レスポンス (200)

```json
{
  "status": "ok",
  "message": "並び順を更新しました"
}
```

---

## ✅ 4. タスクAPI

### 4.1 タスク作成

```http
POST /categories/{category}/tasks
```

#### リクエストパラメータ

```json
{
  "title": "新しいタスク",
  "description": "タスクの詳細説明",
  "due_date": "2025-01-15T00:00:00.000000Z",
  "priority": "high"
}
```

#### バリデーション規則

- `title`: 必須、255文字以内
- `description`: オプション、1000文字以内
- `due_date`: オプション、日付形式
- `priority`: オプション、enum（low, medium, high）

#### 成功レスポンス (201)

```json
{
  "success": true,
  "task": {
    "id": 1,
    "title": "新しいタスク",
    "description": "タスクの詳細説明",
    "is_done": false,
    "due_date": "2025-01-15T00:00:00.000000Z",
    "priority": "high",
    "category_id": 1,
    "created_at": "2025-01-01T00:00:00.000000Z",
    "updated_at": "2025-01-01T00:00:00.000000Z"
  },
  "message": "タスクを作成しました"
}
```

### 4.2 タスク完了/未完了切り替え

```http
PATCH /tasks/{task}/toggle
```

#### 成功レスポンス (200)

```json
{
  "success": true,
  "task": {
    "id": 1,
    "title": "タスクタイトル",
    "is_done": true,
    "updated_at": "2025-01-01T12:00:00.000000Z"
  },
  "message": "タスクを完了しました"
}
```

### 4.3 タスク更新

```http
PATCH /tasks/{task}
```

#### リクエストパラメータ

```json
{
  "title": "更新されたタスク",
  "description": "新しい説明",
  "due_date": "2025-01-20T00:00:00.000000Z",
  "priority": "medium"
}
```

#### 成功レスポンス (200)

```json
{
  "success": true,
  "task": {
    "id": 1,
    "title": "更新されたタスク",
    "description": "新しい説明",
    "due_date": "2025-01-20T00:00:00.000000Z",
    "priority": "medium",
    "updated_at": "2025-01-01T12:00:00.000000Z"
  },
  "message": "タスクを更新しました"
}
```

### 4.4 タスク削除

```http
DELETE /tasks/{task}
```

#### 成功レスポンス (204)

```json
{
  "success": true,
  "message": "タスクを削除しました"
}
```

---

## 🤝 5. 共有API

### 5.1 カテゴリ共有

```http
POST /categories/{category}/share
```

#### リクエストパラメータ

```json
{
  "email": "share@example.com",
  "permission": "edit",
  "message": "カテゴリを共有します"
}
```

#### バリデーション規則

- `email`: 必須、メール形式
- `permission`: 必須、enum（view, edit）
- `message`: オプション、500文字以内

#### 成功レスポンス (200)

```json
{
  "success": true,
  "message": "共有招待を送信しました",
  "invitation": {
    "email": "share@example.com",
    "permission": "edit",
    "token": "abc123...",
    "expires_at": "2025-01-08T00:00:00.000000Z"
  }
}
```

### 5.2 共有確認

```http
GET /categories/share/confirm/{token}
```

#### パスパラメータ

- `token`: 共有トークン

#### 成功レスポンス (200)

```json
{
  "success": true,
  "category": {
    "id": 1,
    "name": "共有カテゴリ",
    "permission": "edit"
  },
  "message": "カテゴリの共有を承認しました"
}
```

### 5.3 共有タスク一覧

```http
GET /shared-tasks
```

#### 成功レスポンス (200)

```json
{
  "shared_categories": [
    {
      "id": 1,
      "name": "共有カテゴリ",
      "owner": {
        "id": 2,
        "name": "オーナー名",
        "email": "owner@example.com"
      },
      "permission": "edit",
      "incomplete_tasks_count": 2,
      "tasks": [
        {
          "id": 1,
          "title": "共有タスク",
          "is_done": false,
          "created_at": "2025-01-01T00:00:00.000000Z"
        }
      ]
    }
  ]
}
```

### 5.4 共有権限更新

```http
PATCH /categories/{category}/share/{user}
```

#### リクエストパラメータ

```json
{
  "permission": "view"
}
```

#### 成功レスポンス (200)

```json
{
  "success": true,
  "message": "共有権限を更新しました"
}
```

### 5.5 共有解除

```http
DELETE /categories/{category}/share/{user}
```

#### 成功レスポンス (200)

```json
{
  "success": true,
  "message": "共有を解除しました"
}
```

---

## 👥 6. 共有グループAPI

### 6.1 共有グループ一覧

```http
GET /share-groups
```

#### 成功レスポンス (200)

```json
{
  "groups": [
    {
      "id": 1,
      "name": "家族",
      "description": "家族タスク管理",
      "created_at": "2025-01-01T00:00:00.000000Z",
      "members": [
        {
          "id": 1,
          "name": "ユーザー1",
          "email": "user1@example.com"
        }
      ]
    }
  ]
}
```

### 6.2 共有グループ作成

```http
POST /share-groups
```

#### リクエストパラメータ

```json
{
  "name": "新しいグループ",
  "description": "グループの説明",
  "members": [
    "user1@example.com",
    "user2@example.com"
  ]
}
```

#### 成功レスポンス (201)

```json
{
  "success": true,
  "group": {
    "id": 1,
    "name": "新しいグループ",
    "description": "グループの説明",
    "created_at": "2025-01-01T00:00:00.000000Z"
  },
  "message": "グループを作成しました"
}
```

### 6.3 共有グループ更新

```http
PATCH /share-groups/{group}
```

#### リクエストパラメータ

```json
{
  "name": "更新されたグループ名",
  "description": "新しい説明"
}
```

#### 成功レスポンス (200)

```json
{
  "success": true,
  "group": {
    "id": 1,
    "name": "更新されたグループ名",
    "description": "新しい説明",
    "updated_at": "2025-01-01T12:00:00.000000Z"
  },
  "message": "グループを更新しました"
}
```

### 6.4 共有グループ削除

```http
DELETE /share-groups/{group}
```

#### 成功レスポンス (204)

```json
{
  "success": true,
  "message": "グループを削除しました"
}
```

---

## 👤 7. プロフィールAPI

### 7.1 プロフィール取得

```http
GET /profile
```

#### 成功レスポンス (200)

```json
{
  "user": {
    "id": 1,
    "name": "ユーザー名",
    "email": "user@example.com",
    "email_verified_at": "2025-01-01T00:00:00.000000Z",
    "created_at": "2025-01-01T00:00:00.000000Z",
    "updated_at": "2025-01-01T00:00:00.000000Z",
    "avatar_url": "/storage/avatars/user_1.png",
    "preferences": {
      "timezone": "Asia/Tokyo",
      "language": "ja",
      "theme": "light"
    }
  }
}
```

### 7.2 プロフィール更新

```http
PATCH /profile
```

#### リクエストパラメータ

```json
{
  "name": "新しい名前",
  "email": "new@example.com",
  "avatar": "file_object",
  "preferences": {
    "timezone": "Asia/Tokyo",
    "language": "ja",
    "theme": "dark"
  }
}
```

#### 成功レスポンス (200)

```json
{
  "success": true,
  "user": {
    "id": 1,
    "name": "新しい名前",
    "email": "new@example.com",
    "avatar_url": "/storage/avatars/user_1_new.png",
    "updated_at": "2025-01-01T12:00:00.000000Z"
  },
  "message": "プロフィールを更新しました"
}
```

### 7.3 アカウント削除

```http
DELETE /profile
```

#### リクエストパラメータ

```json
{
  "password": "current_password",
  "confirmation": "DELETE_ACCOUNT"
}
```

#### 成功レスポンス (204)

```json
{
  "success": true,
  "message": "アカウントを削除しました"
}
```

---

## 🔍 8. 検索・フィルタAPI

### 8.1 タスク検索

```http
GET /search/tasks
```

#### クエリパラメータ

- `q`: 検索キーワード
- `category_id`: カテゴリID
- `status`: タスクステータス（all, pending, completed）
- `priority`: 優先度（low, medium, high）
- `due_date_from`: 期限開始日
- `due_date_to`: 期限終了日
- `limit`: 結果数制限（デフォルト20）
- `offset`: オフセット

#### 成功レスポンス (200)

```json
{
  "tasks": [
    {
      "id": 1,
      "title": "検索結果のタスク",
      "description": "説明",
      "is_done": false,
      "due_date": "2025-01-15T00:00:00.000000Z",
      "priority": "high",
      "category": {
        "id": 1,
        "name": "カテゴリ名"
      }
    }
  ],
  "total": 25,
  "limit": 20,
  "offset": 0
}
```

### 8.2 カテゴリ検索

```http
GET /search/categories
```

#### クエリパラメータ

- `q`: 検索キーワード
- `shared`: 共有カテゴリを含むか（true, false）

#### 成功レスポンス (200)

```json
{
  "categories": [
    {
      "id": 1,
      "name": "検索結果のカテゴリ",
      "incomplete_tasks_count": 3,
      "is_shared": false
    }
  ],
  "total": 5
}
```

---

## 📊 9. 統計・分析API

### 9.1 ダッシュボード統計

```http
GET /stats/dashboard
```

#### 成功レスポンス (200)

```json
{
  "stats": {
    "total_categories": 10,
    "total_tasks": 50,
    "completed_tasks": 30,
    "pending_tasks": 20,
    "completion_rate": 60.0,
    "tasks_this_week": 15,
    "tasks_this_month": 35
  },
  "chart_data": {
    "daily_completion": [
      {"date": "2025-01-01", "completed": 5},
      {"date": "2025-01-02", "completed": 3}
    ],
    "category_distribution": [
      {"category": "仕事", "count": 15},
      {"category": "プライベート", "count": 10}
    ]
  }
}
```

### 9.2 月別統計

```http
GET /stats/monthly
```

#### クエリパラメータ

- `year`: 年（デフォルト：現在年）
- `month`: 月（デフォルト：現在月）

#### 成功レスポンス (200)

```json
{
  "period": "2025-01",
  "stats": {
    "total_tasks": 25,
    "completed_tasks": 20,
    "pending_tasks": 5,
    "completion_rate": 80.0,
    "avg_tasks_per_day": 0.8
  },
  "daily_breakdown": [
    {"date": "2025-01-01", "created": 2, "completed": 1},
    {"date": "2025-01-02", "created": 3, "completed": 2}
  ]
}
```

---

## 🔗 10. Webhook・外部連携API

### 10.1 Googleカレンダー連携

```http
POST /integrations/google-calendar/sync
```

#### リクエストパラメータ

```json
{
  "task_id": 1,
  "calendar_id": "primary",
  "event_title": "会議準備",
  "start_time": "2025-01-15T09:00:00.000000Z",
  "end_time": "2025-01-15T10:00:00.000000Z"
}
```

#### 成功レスポンス (200)

```json
{
  "success": true,
  "event": {
    "id": "google_event_id",
    "link": "https://calendar.google.com/event?eid=...",
    "created_at": "2025-01-01T00:00:00.000000Z"
  },
  "message": "Googleカレンダーに追加しました"
}
```

---

## 🔧 11. システム・設定API

### 11.1 システム情報

```http
GET /system/info
```

#### 成功レスポンス (200)

```json
{
  "version": "1.0.0",
  "environment": "production",
  "maintenance_mode": false,
  "features": {
    "sharing": true,
    "mobile_app": true,
    "google_integration": true
  },
  "limits": {
    "max_categories": 100,
    "max_tasks_per_category": 1000,
    "max_file_size": "10MB"
  }
}
```

### 11.2 アプリケーション設定

```http
GET /settings/app
```

#### 成功レスポンス (200)

```json
{
  "settings": {
    "app_name": "TASTACK2",
    "app_version": "1.0.0",
    "timezone": "Asia/Tokyo",
    "date_format": "Y-m-d",
    "time_format": "H:i:s",
    "supported_languages": ["ja", "en"],
    "default_language": "ja"
  }
}
```

---

## 📱 12. モバイル固有API

### 12.1 モバイル設定同期

```http
POST /mobile/sync
```

#### リクエストパラメータ

```json
{
  "device_id": "device_unique_id",
  "platform": "android",
  "version": "1.0.0",
  "settings": {
    "notifications": true,
    "offline_mode": true,
    "sync_frequency": 300
  }
}
```

#### 成功レスポンス (200)

```json
{
  "success": true,
  "sync_token": "sync_token_here",
  "last_sync": "2025-01-01T12:00:00.000000Z",
  "message": "設定を同期しました"
}
```

### 12.2 オフライン同期

```http
POST /mobile/offline-sync
```

#### リクエストパラメータ

```json
{
  "last_sync": "2025-01-01T10:00:00.000000Z",
  "changes": [
    {
      "type": "task",
      "action": "create",
      "data": {
        "title": "オフラインタスク",
        "category_id": 1
      }
    }
  ]
}
```

#### 成功レスポンス (200)

```json
{
  "success": true,
  "conflicts": [],
  "updated_data": {
    "categories": [],
    "tasks": []
  },
  "last_sync": "2025-01-01T12:00:00.000000Z"
}
```

---

## 🛡️ セキュリティ

### レート制限

| エンドポイント | 制限 |
|---------------|------|
| 認証関連 | 5回/分 |
| 一般API | 60回/分 |
| 検索API | 30回/分 |
| ファイルアップロード | 10回/分 |

### セキュリティヘッダー

```http
X-Content-Type-Options: nosniff
X-Frame-Options: DENY
X-XSS-Protection: 1; mode=block
Strict-Transport-Security: max-age=31536000; includeSubDomains
```

### データ暗号化

- パスワード: bcrypt
- セッション: 暗号化
- ファイル: ストレージ暗号化（本番環境）

---

## 🔍 テスト・デバッグ

### デバッグモード

開発環境では`APP_DEBUG=true`設定により、詳細なエラー情報が返されます。

### APIテスト

```bash
# PHPUnit テスト実行
php artisan test

# 特定のテストクラス実行
php artisan test --filter=CategoryTest

# カバレッジレポート生成
php artisan test --coverage-html coverage/
```

### ログ出力

```php
// ログレベル
Log::emergency($message);
Log::alert($message);
Log::critical($message);
Log::error($message);
Log::warning($message);
Log::notice($message);
Log::info($message);
Log::debug($message);
```

---

## 📝 よくある質問

### Q1: CSRFトークンはどこで取得できますか？

A1: HTMLのmeta要素から取得できます。

```html
<meta name="csrf-token" content="token_value">
```

### Q2: ファイルアップロードの制限はありますか？

A2: 最大10MBまで、jpg/png/gif形式のみ対応しています。

### Q3: APIのバージョン管理はどうなっていますか？

A3: 現在はv1のみですが、将来的に`/api/v2/`のようなバージョン管理を予定しています。

### Q4: レスポンス時間の目安は？

A4:

- 一般的なAPI: 200ms以下
- 検索API: 500ms以下
- ファイルアップロード: 2s以下

---

## 🔗 関連リンク

- [開発者ガイド](./api-quickstart.md)
- [Postmanコレクション](./TASTACK2_API.postman_collection.json)
- [OpenAPI仕様](./openapi.yaml)
- [セットアップガイド](../README.md)

---

## 📋 変更履歴

| バージョン | 日付 | 変更内容 |
|-----------|------|----------|
| 1.0.0 | 2025-01-10 | 初版リリース |
| 1.0.1 | 2025-01-10 | 統計API追加 |
| 1.0.2 | 2025-01-10 | モバイル同期API追加 |

---

*このドキュメントは自動生成される部分があります。最新の情報は[GitHubリポジトリ](https://github.com/your-org/tastack2)を参照してください。*
