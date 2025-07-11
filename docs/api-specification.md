# TASTACK2 API仕様書

## 概要

TASTACK2は、Laravel + Capacitor.jsベースのクロスプラットフォーム対応タスク管理システムです。
本仕様書では、Webアプリケーションおよびモバイルアプリケーションで使用されるAPIエンドポイントについて詳細に記述します。

## ベースURL

- **開発環境**: `http://localhost:8081`
- **本番環境**: `https://your-domain.com`
ドキュメント: API仕様書の整備
## 認証

### 認証方式

- **Web**: Laravel Sanctumによるセッション認証
- **API**: Bearerトークン認証（将来実装予定）

### 必要なヘッダー

```http
Content-Type: application/json
Accept: application/json
X-CSRF-TOKEN: {csrf_token}
```

## エラーレスポンス

### 共通エラーフォーマット

```json
{
  "error": "エラーメッセージ",
  "message": "詳細なエラー説明",
  "status": 400
}
```

### HTTPステータスコード

- `200` - 成功
- `201` - 作成成功
- `400` - バリデーションエラー
- `401` - 認証エラー
- `403` - 権限エラー
- `404` - リソースが見つからない
- `422` - バリデーションエラー（詳細）
- `500` - サーバーエラー

---

## 1. 認証API

### 1.1 ログイン


```http
POST /login
```


**リクエスト**

```json
{
  "email": "user@example.com",
  "password": "password123"
}
```


**レスポンス**

```json
{
  "success": true,
  "redirect": "/dashboard"
}

```

### 1.2 ログアウト

```http

POST /logout
```

**レスポンス**

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

**リクエスト**

```json
{
  "name": "ユーザー名",

  "email": "user@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```


### 1.4 SNSログイン

```http
GET /login/{provider}
```


**パラメータ**

- `provider`: `google` (現在はGoogleのみ対応)

---


## 2. ダッシュボードAPI

### 2.1 ダッシュボード情報取得

```http
GET /dashboard
```

**レスポンス**

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
      "name": "チーム共有",
      "incomplete_tasks_count": 1,
      "pivot": {
        "permission": "edit"

      }
    }
  ]
}
```


---

## 3. カテゴリAPI

### 3.1 カテゴリ一覧取得

```http
GET /categories
```

**レスポンス**

```json
{
  "data": [
    {

      "id": 1,
      "name": "仕事",
      "display_order": 1,
      "icon_path": "/storage/icons/work.png",
      "incomplete_tasks_count": 3,

      "created_at": "2025-01-01T00:00:00.000000Z",
      "updated_at": "2025-01-01T00:00:00.000000Z"
    }
  ]
}
```

### 3.2 カテゴリ作成


```http
POST /categories
```

**リクエスト**

```json
{
  "name": "新しいカテゴリ",
  "icon": "base64_encoded_image_data"
}
```


**レスポンス**

```json
{
  "success": true,

  "message": "カテゴリを作成しました",
  "data": {
    "id": 1,
    "name": "新しいカテゴリ",
    "display_order": 1,
    "icon_path": "/storage/icons/category_1.png"
  }
}
```

### 3.3 カテゴリ詳細取得

```http
GET /categories/{category}
```

**レスポンス**

```json
{
  "category": {
    "id": 1,
    "name": "仕事",
    "icon_path": "/storage/icons/work.png",
    "is_owner": true
  },
  "tasks": [
    {
      "id": 1,
      "title": "プレゼン資料作成",
      "due_date": "2025-07-15 10:00:00",
      "note": "月末までに完成させる",
      "is_done": false,
      "used_ai_advisor": true,

      "ai_advice": "効率的な作業手順の提案",
      "created_at": "2025-01-01T00:00:00.000000Z"
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
```

### 3.4 カテゴリ更新


```http
PATCH /categories/{category}
```

**リクエスト**

```json

{
  "name": "更新されたカテゴリ名",
  "icon": "base64_encoded_image_data"
}
```


### 3.5 カテゴリ削除

```http
DELETE /categories/{category}
```

**レスポンス**

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

**リクエスト**

```json
{
  "orders": [
    {"id": 1, "order": 1},
    {"id": 2, "order": 2}
  ]

}
```

---

## 4. タスクAPI

### 4.1 タスク作成

```http
POST /categories/{category}/tasks
```

**リクエスト**


```json
{
  "title": "新しいタスク",
  "due_date": "2025-07-15 10:00:00",

  "note": "タスクの詳細説明",
  "use_ai_advisor": true,
  "add_to_google_calendar": false
}
```

**レスポンス**

```json

{
  "success": true,
  "message": "タスクを追加しました",
  "data": {
    "id": 1,

    "title": "新しいタスク",
    "due_date": "2025-07-15 10:00:00",
    "ai_advice": "AIからのアドバイス（利用時のみ）"
  }
}
```

### 4.2 タスク完了状態切り替え

```http

PATCH /tasks/{task}/toggle
```

**レスポンス**


```json
{
  "success": true,
  "message": "タスクの完了状態を変更しました",
  "is_done": true
}
```

### 4.3 タスク更新

```http
PATCH /tasks/{task}

```

**リクエスト**

```json

{
  "title": "更新されたタスク",
  "due_date": "2025-07-16 15:00:00",
  "note": "更新された説明",
  "done_comment": "完了時のコメント"
}
```


### 4.4 タスク削除

```http
DELETE /tasks/{task}
```

**レスポンス**


```json
{
  "success": true,
  "message": "タスクを削除しました"
}

```

---

## 5. 共有機能API

### 5.1 カテゴリ共有


```http
POST /categories/{category}/share
```


**リクエスト**

```json
{
  "identifiers": ["user@example.com", "account123"],
  "permissions": ["edit", "view"]
}
```


**レスポンス**

```json
{

  "success": true,
  "message": "2件の共有リンクを送信しました"
}
```

### 5.2 共有権限更新

```http
PATCH /categories/{category}/share/{user}

```

**リクエスト**

```json

{
  "permission": "full"
}
```

### 5.3 共有解除

```http
DELETE /categories/{category}/share/{user}
```

**レスポンス**

```json
{
  "success": true,
  "message": "共有を解除しました"
}
```


### 5.4 共有確認

```http
GET /categories/share/confirm/{token}
```


**レスポンス**

```json
{
  "success": true,
  "message": "共有が承認されました",
  "redirect": "/shared/{token}"
}
```

### 5.5 共有タスク一覧

```http
GET /shared-tasks
```

**レスポンス**

```json
{

  "shared_categories": [
    {
      "id": 1,
      "name": "共有カテゴリ",
      "incomplete_tasks_count": 2,

      "pivot": {
        "permission": "edit"
      }
    }
  ]
}
```


---

## 6. 共有グループAPI

### 6.1 共有グループ一覧


```http
GET /share-groups
```

**レスポンス**

```json

{
  "groups": [
    {
      "id": 1,
      "name": "チームA",
      "members": [
        {
          "id": 1,
          "identifier": "user1@example.com",

          "user": {
            "name": "ユーザー1"
          }
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

**リクエスト**

```json
{
  "name": "新しいグループ",

  "identifiers": ["user1@example.com", "user2@example.com"]
}
```

### 6.3 共有グループ更新


```http
PATCH /share-groups/{group}
```

**リクエスト**

```json
{
  "name": "更新されたグループ名",
  "identifiers": ["user1@example.com", "user3@example.com"]

}
```

### 6.4 共有グループ削除


```http
DELETE /share-groups/{group}
```

---

## 7. プロフィールAPI

### 7.1 プロフィール情報取得

```http
GET /profile
```


**レスポンス**

```json
{

  "user": {
    "id": 1,
    "name": "ユーザー名",
    "email": "user@example.com",
    "account_id": "user123",
    "button_layout": "menu_above",
    "google_calendar_color": "#4285f4",
    "ai_advisor_used_today": 2,
    "ai_advisor_limit_per_day": 5
  }
}
```


### 7.2 プロフィール更新

```http
PATCH /profile
```


**リクエスト**

```json
{
  "name": "更新されたユーザー名",
  "email": "updated@example.com",
  "account_id": "newid123",

  "button_layout": "menu_under",
  "google_calendar_color": "#34a853"
}
```

### 7.3 パスワード更新

```http
PUT /password
```

**リクエスト**


```json
{
  "current_password": "current_password",
  "password": "new_password",
  "password_confirmation": "new_password"

}
```

---


## 8. モバイル機能API

### 8.1 デバイス情報取得

```http
GET /mobile-test
```

**レスポンス**


```json
{
  "device_info": {

    "platform": "ios",
    "model": "iPhone 12",
    "operating_system": "iOS",
    "os_version": "15.0",
    "is_web": false
  }

}
```

### 8.2 カメラ機能

```http

POST /api/camera/capture
```

**リクエスト**

```json
{
  "quality": 80,

  "source": "camera"
}
```


**レスポンス**

```json
{

  "success": true,
  "image_data": "base64_encoded_image_data",
  "format": "jpeg"
}
```

---


## 9. 静的ページAPI

### 9.1 利用規約

```http

GET /termsofservice
```

### 9.2 プライバシーポリシー

```http
GET /privacypolicy
```


### 9.3 運営会社情報

```http
GET /company
```

---


## 10. バリデーションルール

### 10.1 カテゴリ

- `name`: 必須、文字列、最大50文字
- `icon`: 画像、最大5MB

### 10.2 タスク


- `title`: 必須、文字列、最大255文字
- `due_date`: 日付形式
- `note`: 文字列
- `use_ai_advisor`: 真偽値

### 10.3 ユーザー

- `name`: 必須、文字列、最大255文字

- `email`: 必須、メール形式、ユニーク
- `account_id`: 必須、英数字、最大10文字、ユニーク
- `password`: 必須、8文字以上、確認必須

### 10.4 共有

- `identifiers`: 必須、配列
- `permissions`: 必須、配列、値は `view`、`edit`、`full` のいずれか

---

## 11. レート制限

### 11.1 ログイン

- 5回の失敗でアカウント一時ロック
- ロック時間: 動的（失敗回数に応じて延長）

### 11.2 AIアドバイザー

- 1日あたりの利用回数制限（デフォルト: 5回）
- ユーザーごとに設定可能

### 11.3 API全般

- 1分間に60リクエスト（認証済みユーザー）
- IPアドレスベースでの制限

---

## 12. Webhook（将来実装予定）

### 12.1 タスク完了通知

```http
POST /webhooks/task-completed
```

### 12.2 共有招待通知

```http
POST /webhooks/share-invitation
```

---

## 13. WebSocket（将来実装予定）

### 13.1 リアルタイム更新

- チャンネル: `category.{category_id}`
- イベント: `task.updated`, `task.created`, `task.deleted`

---

## 付録

### A. 環境変数

```env
APP_URL=http://localhost:8081
DB_CONNECTION=mysql
MAIL_MAILER=smtp
GOOGLE_CLIENT_ID=your_google_client_id
GOOGLE_CLIENT_SECRET=your_google_client_secret
```

### B. データベース構造

- `users`: ユーザー情報
- `categories`: カテゴリ情報
- `tasks`: タスク情報
- `category_user_shares`: カテゴリ共有情報
- `share_groups`: 共有グループ
- `share_group_members`: 共有グループメンバー

### C. ファイル構造

```
app/
├── Http/Controllers/
│   ├── CategoryController.php
│   ├── TaskController.php
│   ├── ShareGroupController.php
│   └── ...
├── Models/
│   ├── Category.php
│   ├── Task.php
│   ├── User.php
│   └── ...
└── ...
```

---

## 更新履歴

- 2025-07-11: 初版作成
- 2025-07-11: 全APIエンドポイントの詳細仕様を追加
- 2025-07-11: バリデーションルールとエラーハンドリングを追加

---

## 連絡先

- **開発者**: TASTACK2 開発チーム
- **バージョン**: v1.0.0
- **最終更新**: 2025年7月11日
