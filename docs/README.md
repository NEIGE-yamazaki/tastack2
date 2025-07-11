# TASTACK2 API ドキュメント 索引

## 📚 ドキュメント一覧

TASTACK2 APIの完全なドキュメントセットです。目的に応じて適切なドキュメントを選択してください。

### 🚀 クイックスタート

- **[API クイックスタート](./api-quickstart.md)**
  - 初回セットアップ手順
  - 基本的な使用方法
  - 簡単なサンプルコード

### 📖 メインドキュメント

- **[API ガイド](./api-guide.md)**
  - 全APIエンドポイントの詳細
  - リクエスト・レスポンス例
  - エラーハンドリング

- **[開発者ガイド](./developer-guide.md)**
  - 実装のベストプラクティス
  - 実用的なコード例
  - トラブルシューティング

### 🛠️ 技術仕様

- **[OpenAPI仕様](./openapi.yaml)**
  - 機械可読なAPI仕様
  - Swagger UIで表示可能
  - 自動コード生成対応

- **[Postmanコレクション](./TASTACK2_API.postman_collection.json)**
  - APIテスト用コレクション
  - 実際のリクエスト例
  - 環境設定込み

### 📄 既存ドキュメント

- **[API仕様書 v1](./api-specification.md)**
  - 初期版API仕様書
  - 日本語での詳細説明

- **[API仕様書 v2](./api-specification-v2.md)**
  - 改良版API仕様書
  - Markdownリント対応

## 🎯 目的別ガイド

### 初めて使う方

1. [API クイックスタート](./api-quickstart.md) - 基本的なセットアップ
2. [API ガイド](./api-guide.md) - 全機能の概要
3. [開発者ガイド](./developer-guide.md) - 実装のコツ

### 開発者向け

1. [開発者ガイド](./developer-guide.md) - 実装パターン
2. [OpenAPI仕様](./openapi.yaml) - 詳細な技術仕様
3. [Postmanコレクション](./TASTACK2_API.postman_collection.json) - テスト環境

### 運用・保守

1. [API ガイド](./api-guide.md) - セキュリティ・制限事項
2. [開発者ガイド](./developer-guide.md) - トラブルシューティング

## 📋 APIエンドポイント概要

### 認証
- `POST /login` - ログイン
- `POST /logout` - ログアウト
- `POST /register` - ユーザー登録

### カテゴリ管理
- `GET /categories` - カテゴリ一覧
- `POST /categories` - カテゴリ作成
- `GET /categories/{id}` - カテゴリ詳細
- `PATCH /categories/{id}` - カテゴリ更新
- `DELETE /categories/{id}` - カテゴリ削除
- `POST /categories/reorder` - 並び替え

### タスク管理
- `POST /categories/{id}/tasks` - タスク作成
- `PATCH /tasks/{id}` - タスク更新
- `PATCH /tasks/{id}/toggle` - 完了切り替え
- `DELETE /tasks/{id}` - タスク削除

### 共有機能
- `POST /categories/{id}/share` - カテゴリ共有
- `GET /shared-tasks` - 共有タスク一覧
- `GET /categories/share/confirm/{token}` - 共有確認

### その他
- `GET /dashboard` - ダッシュボード
- `GET /profile` - プロフィール取得
- `PATCH /profile` - プロフィール更新
- `GET /search/tasks` - タスク検索
- `GET /stats/dashboard` - 統計情報

## 🔧 開発環境

### 必要なツール

- PHP 8.1以上
- Composer
- Node.js 18以上
- Docker Desktop（オプション）

### 設定

```bash
# 開発サーバー起動
php artisan serve

# フロントエンド監視
npm run dev

# テスト実行
php artisan test
```

## 📝 更新履歴

### v1.1.0 (2025-01-10)
- 統計API追加
- モバイル同期API追加
- OpenAPI仕様更新
- 開発者ガイド作成

### v1.0.0 (2025-01-10)
- 初回リリース
- 基本的なCRUD操作
- 認証システム
- 共有機能

## 🔗 関連リンク

- [GitHubリポジトリ](https://github.com/your-org/tastack2)
- [Laravel Documentation](https://laravel.com/docs)
- [Capacitor Documentation](https://capacitorjs.com/docs)
- [プロジェクトWiki](https://github.com/your-org/tastack2/wiki)

## 📞 サポート

問題や質問がある場合：

1. [GitHub Issues](https://github.com/your-org/tastack2/issues)
2. [開発者向けFAQ](./api-guide.md#よくある質問)
3. [プロジェクトDiscussions](https://github.com/your-org/tastack2/discussions)

---

*このドキュメントは継続的に更新されます。最新の情報は [GitHubリポジトリ](https://github.com/your-org/tastack2) を確認してください。*
