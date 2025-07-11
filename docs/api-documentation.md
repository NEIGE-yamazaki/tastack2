# TASTACK2 API 仕様書

## 概要

TASTACK2は、Laravel + Capacitor.jsで構築されたクロスプラットフォーム対応タスク管理システムです。
本仕様書では、全APIエンドポイントの詳細を説明します。

## 基本情報

- **フレームワーク**: Laravel 10 + Capacitor.js
- **認証**: Laravel Sanctum
- **データベース**: MySQL/PostgreSQL
- **開発環境**: <http://localhost:8081>
- **本番環境**: <https://api.tastack2.com>

## 認証

### 必要なヘッダー

```http
Content-Type: application/json
Accept: application/json
X-CSRF-TOKEN: {csrf_token}
```

### CSRFトークン取得

```javascript
const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
```

## エラーレスポンス

### 共通エラー形式

```json
{
  "error": "エラーメッセージ",
  "message": "詳細説明",
  "status": 400,
  "errors": {
    "field": ["フィールドエラー"]
  }
}
```

### ステータスコード

| コード | 説明 |
|--------|------|
| 200 | 成功 |
| 201 | 作成成功 |
| 204 | 削除成功 |
| 400 | リクエストエラー |
| 401 | 認証エラー |
| 403 | 権限エラー |
| 404 | 見つからない |
| 422 | バリデーションエラー |
| 500 | サーバーエラー |

---

## 認証 API

### ログイン

```http
POST /login
```

#### リクエスト

```json
{
  "email": "user@example.com",
  "password": "password123",
  "remember": true
}
```

#### レスポンス

```json
{
  "success": true,
  "redirect": "/dashboard",
  "user": {
    "id": 1,
    "name": "ユーザー名",
    "email": "user@example.com"
  }
}
```

### ログアウト

```http
POST /logout
```

#### レスポンス

```json
{
  "success": true,
  "redirect": "/"
}
```

### ユーザー登録

```http
POST /register
```

#### リクエスト

```json
{
  "name": "ユーザー名",
  "email": "user@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

#### レスポンス

```json
{
  "success": true,
  "redirect": "/email/verify",
  "message": "確認メールを送信しました"
}
```

---

## ダッシュボード API

### ダッシュボード情報取得

```http
GET /dashboard
```

#### レスポンス

```json
{
  "ownCategories": [
    {
      "id": 1,
      "name": "仕事",
      "display_order": 1,
      "icon_path": "/storage/icons/work.png",
      "incomplete_tasks_count": 3,
      "created_at": "2025-01-01T00:00:00.000000Z"
    }
  ],
  "sharedCategories": [
    {
      "id": 2,
      "name": "共有カテゴリ",
      "incomplete_tasks_count": 1,
      "pivot": {
        "permission": "edit"
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

## カテゴリ API

### カテゴリ一覧取得

```http
GET /categories
```

#### クエリパラメータ

- `sort`: 並び順 (display_order, created_at, name)
- `order`: 昇順/降順 (asc, desc)
- `with_tasks`: タスクを含む (true, false)

#### レスポンス

```json
{
  "categories": [
    {
      "id": 1,
      "name": "仕事",
      "display_order": 1,
      "icon_path": "/storage/icons/work.png",
      "incomplete_tasks_count": 3,
      "tasks": [
        {
          "id": 1,
          "title": "会議準備",
          "is_done": false
        }
      ]
    }
  ]
}
```

### カテゴリ作成

```http
POST /categories
```

#### リクエスト

```json
{
  "name": "新しいカテゴリ",
  "icon": "file_object_or_path",
  "display_order": 1
}
```

#### バリデーション

- `name`: 必須、255文字以内
- `icon`: オプション、画像ファイル
- `display_order`: オプション、整数

#### レスポンス

```json
{
  "success": true,
  "category": {
    "id": 1,
    "name": "新しいカテゴリ",
    "display_order": 1,
    "icon_path": "/storage/icons/category_1.png"
  },
  "message": "カテゴリを作成しました"
}
```

### カテゴリ詳細取得

```http
GET /categories/{category}
```

#### レスポンス

```json
{
  "category": {
    "id": 1,
    "name": "仕事",
    "display_order": 1,
    "icon_path": "/storage/icons/work.png",
    "tasks": [
      {
        "id": 1,
        "title": "会議準備",
        "description": "資料作成",
        "is_done": false,
        "due_date": "2025-01-15T00:00:00.000000Z"
      }
    ],
    "shared_users": [
      {
        "id": 2,
        "name": "共有ユーザー",
        "email": "shared@example.com",
        "pivot": {
          "permission": "edit"
        }
      }
    ]
  }
}
```

### カテゴリ更新

```http
PATCH /categories/{category}
```

#### リクエスト

```json
{
  "name": "更新されたカテゴリ名",
  "icon": "file_object_or_path",
  "display_order": 2
}
```

#### レスポンス

```json
{
  "success": true,
  "category": {
    "id": 1,
    "name": "更新されたカテゴリ名",
    "display_order": 2,
    "icon_path": "/storage/icons/updated.png"
  },
  "message": "カテゴリを更新しました"
}
```

### カテゴリ削除

```http
DELETE /categories/{category}
```

#### レスポンス

```json
{
  "success": true,
  "message": "カテゴリを削除しました"
}
```

### カテゴリ並び替え

```http
POST /categories/reorder
```

#### リクエスト

```json
{
  "orders": [
    {"id": 1, "order": 1},
    {"id": 2, "order": 2},
    {"id": 3, "order": 3}
  ]
}
```

#### レスポンス

```json
{
  "status": "ok",
  "message": "並び順を更新しました"
}
```

---

## タスク API

### タスク作成

```http
POST /categories/{category}/tasks
```

#### リクエスト

```json
{
  "title": "新しいタスク",
  "description": "タスクの詳細説明",
  "due_date": "2025-01-15T00:00:00.000000Z",
  "priority": "high"
}
```

#### バリデーション

- `title`: 必須、255文字以内
- `description`: オプション、1000文字以内
- `due_date`: オプション、日付形式
- `priority`: オプション、enum(low, medium, high)

#### レスポンス

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
    "category_id": 1
  },
  "message": "タスクを作成しました"
}
```

### タスク完了切り替え

```http
PATCH /tasks/{task}/toggle
```

#### レスポンス

```json
{
  "success": true,
  "task": {
    "id": 1,
    "title": "タスクタイトル",
    "is_done": true
  },
  "message": "タスクを完了しました"
}
```

### タスク更新

```http
PATCH /tasks/{task}
```

#### リクエスト

```json
{
  "title": "更新されたタスク",
  "description": "新しい説明",
  "due_date": "2025-01-20T00:00:00.000000Z",
  "priority": "medium"
}
```

#### レスポンス

```json
{
  "success": true,
  "task": {
    "id": 1,
    "title": "更新されたタスク",
    "description": "新しい説明",
    "due_date": "2025-01-20T00:00:00.000000Z",
    "priority": "medium"
  },
  "message": "タスクを更新しました"
}
```

### タスク削除

```http
DELETE /tasks/{task}
```

#### レスポンス

```json
{
  "success": true,
  "message": "タスクを削除しました"
}
```

---

## 共有 API

### カテゴリ共有

```http
POST /categories/{category}/share
```

#### リクエスト

```json
{
  "email": "share@example.com",
  "permission": "edit",
  "message": "カテゴリを共有します"
}
```

#### バリデーション

- `email`: 必須、メール形式
- `permission`: 必須、enum(view, edit)
- `message`: オプション、500文字以内

#### レスポンス

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

### 共有確認

```http
GET /categories/share/confirm/{token}
```

#### レスポンス

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

### 共有タスク一覧

```http
GET /shared-tasks
```

#### レスポンス

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
          "is_done": false
        }
      ]
    }
  ]
}
```

### 共有権限更新

```http
PATCH /categories/{category}/share/{user}
```

#### リクエスト

```json
{
  "permission": "view"
}
```

#### レスポンス

```json
{
  "success": true,
  "message": "共有権限を更新しました"
}
```

### 共有解除

```http
DELETE /categories/{category}/share/{user}
```

#### レスポンス

```json
{
  "success": true,
  "message": "共有を解除しました"
}
```

---

## 共有グループ API

### 共有グループ一覧

```http
GET /share-groups
```

#### レスポンス

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

### 共有グループ作成

```http
POST /share-groups
```

#### リクエスト

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

#### レスポンス

```json
{
  "success": true,
  "group": {
    "id": 1,
    "name": "新しいグループ",
    "description": "グループの説明"
  },
  "message": "グループを作成しました"
}
```

### 共有グループ更新

```http
PATCH /share-groups/{group}
```

#### リクエスト

```json
{
  "name": "更新されたグループ名",
  "description": "新しい説明"
}
```

#### レスポンス

```json
{
  "success": true,
  "group": {
    "id": 1,
    "name": "更新されたグループ名",
    "description": "新しい説明"
  },
  "message": "グループを更新しました"
}
```

### 共有グループ削除

```http
DELETE /share-groups/{group}
```

#### レスポンス

```json
{
  "success": true,
  "message": "グループを削除しました"
}
```

---

## プロフィール API

### プロフィール取得

```http
GET /profile
```

#### レスポンス

```json
{
  "user": {
    "id": 1,
    "name": "ユーザー名",
    "email": "user@example.com",
    "email_verified_at": "2025-01-01T00:00:00.000000Z",
    "avatar_url": "/storage/avatars/user_1.png",
    "preferences": {
      "timezone": "Asia/Tokyo",
      "language": "ja",
      "theme": "light"
    }
  }
}
```

### プロフィール更新

```http
PATCH /profile
```

#### リクエスト

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

#### レスポンス

```json
{
  "success": true,
  "user": {
    "id": 1,
    "name": "新しい名前",
    "email": "new@example.com",
    "avatar_url": "/storage/avatars/user_1_new.png"
  },
  "message": "プロフィールを更新しました"
}
```

### アカウント削除

```http
DELETE /profile
```

#### リクエスト

```json
{
  "password": "current_password",
  "confirmation": "DELETE_ACCOUNT"
}
```

#### レスポンス

```json
{
  "success": true,
  "message": "アカウントを削除しました"
}
```

---

## 検索 API

### タスク検索

```http
GET /search/tasks
```

#### クエリパラメータ

- `q`: 検索キーワード
- `category_id`: カテゴリID
- `status`: タスクステータス(all, pending, completed)
- `priority`: 優先度(low, medium, high)
- `due_date_from`: 期限開始日
- `due_date_to`: 期限終了日
- `limit`: 結果数制限(デフォルト20)
- `offset`: オフセット

#### レスポンス

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

### カテゴリ検索

```http
GET /search/categories
```

#### クエリパラメータ

- `q`: 検索キーワード
- `shared`: 共有カテゴリを含む(true, false)

#### レスポンス

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

## 統計 API

### ダッシュボード統計

```http
GET /stats/dashboard
```

#### レスポンス

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

---

## モバイル API

### モバイル設定同期

```http
POST /mobile/sync
```

#### リクエスト

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

#### レスポンス

```json
{
  "success": true,
  "sync_token": "sync_token_here",
  "last_sync": "2025-01-01T12:00:00.000000Z",
  "message": "設定を同期しました"
}
```

### オフライン同期

```http
POST /mobile/offline-sync
```

#### リクエスト

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

#### レスポンス

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

## セキュリティ

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

- **パスワード**: bcrypt
- **セッション**: 暗号化
- **ファイル**: ストレージ暗号化(本番環境)

---

## よくある質問

### Q1: CSRFトークンの取得方法

HTMLのmeta要素から取得:

```html
<meta name="csrf-token" content="token_value">
```

### Q2: ファイルアップロード制限

- **最大サイズ**: 10MB
- **対応形式**: jpg, png, gif

### Q3: レスポンス時間目安

- **一般API**: 200ms以下
- **検索API**: 500ms以下
- **ファイルアップロード**: 2s以下

---

## 関連リンク

- [開発者ガイド](./api-quickstart.md)
- [Postmanコレクション](./TASTACK2_API.postman_collection.json)
- [OpenAPI仕様](./openapi.yaml)
- [セットアップガイド](../README.md)

---

## 変更履歴

| バージョン | 日付 | 変更内容 |
|-----------|------|----------|
| 1.0.0 | 2025-01-10 | 初版作成 |
| 1.1.0 | 2025-01-10 | 統計・モバイルAPI追加 |

---

*このドキュメントは定期的に更新されます。最新情報は[GitHubリポジトリ](https://github.com/your-org/tastack2)を確認してください。*
