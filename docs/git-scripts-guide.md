# Git Shell Scripts Collection

## 概要

このディレクトリには、Git操作を効率化するための包括的なシェルスクリプト集が含まれています。

## 📁 スクリプト一覧

### 1. `git-integration.sh` - 統合アクセスポイント
**メインスクリプト** - 他のすべてのGitスクリプトへの統一アクセス

```bash
# 基本的な使用方法
./scripts/git-integration.sh

# クイックコマンド
./scripts/git-integration.sh st        # ステータス確認
./scripts/git-integration.sh co "メッセージ"  # クイックコミット
./scripts/git-integration.sh ps        # 安全プッシュ
```

### 2. `git-helper.sh` - Git基本操作ヘルパー
日常的なGit操作を簡単にするヘルパー関数

**主な機能:**
- 美しいGit状態表示
- 統計情報の表示
- ブランチ情報の整理
- 変更詳細の表示
- クイックコミット
- 安全なプッシュ

```bash
./scripts/git-helper.sh status    # 状態確認
./scripts/git-helper.sh stats     # 統計情報
./scripts/git-helper.sh log       # 美しいログ表示
```

### 3. `git-workflow.sh` - Gitワークフロー管理
Git Flow風のワークフロー管理

**主な機能:**
- 機能ブランチの作成・完了
- リリース準備・完了
- ホットフィックス作成
- 作業のバックアップ
- 同期とクリーンアップ

```bash
./scripts/git-workflow.sh feature "新機能名"    # 機能ブランチ作成
./scripts/git-workflow.sh finish-feature       # 機能ブランチ完了
./scripts/git-workflow.sh prepare-release "v1.0.0"  # リリース準備
```

### 4. `git-automation.sh` - Git自動化
反復的なGitタスクの自動化

**主な機能:**
- WIP自動コミット
- 安全な自動プッシュ
- 定期的なバックアップ
- プロジェクト健全性チェック
- 統計レポート生成
- 監視モード

```bash
./scripts/git-automation.sh wip       # WIP自動コミット
./scripts/git-automation.sh watch     # 監視モード開始
./scripts/git-automation.sh health    # 健全性チェック
```

## 🚀 クイックスタート

### 1. 基本セットアップ
```bash
# スクリプトに実行権限を付与
chmod +x scripts/git-*.sh

# エイリアスを設定
./scripts/git-integration.sh setup
```

### 2. 基本的な使用方法
```bash
# 統合スクリプトのヘルプを表示
./scripts/git-integration.sh

# 現在の状態を確認
./scripts/git-integration.sh st

# 変更をクイックコミット
./scripts/git-integration.sh co "修正内容"

# 安全にプッシュ
./scripts/git-integration.sh ps
```

### 3. 機能開発ワークフロー
```bash
# 新機能の開発開始
./scripts/git-integration.sh feat "ユーザー認証"

# 作業中の自動コミット
./scripts/git-integration.sh wip

# 機能完了
./scripts/git-integration.sh finish
```

## 🔧 VSCodeタスク

以下のVSCodeタスクが利用可能です：

- **Git: 統合ヘルパー** - メインメニューを表示
- **Git: ステータス確認** - 現在の状態を確認
- **Git: 安全プッシュ** - 安全にプッシュ
- **Git: WIP自動コミット** - 作業中の変更を自動コミット
- **Git: プロジェクト健全性チェック** - プロジェクトの問題をチェック
- **Git: 統計レポート生成** - 詳細な統計レポートを生成

## 📋 エイリアス一覧

セットアップ後に使用可能なエイリアス：

```bash
g           # git-integration.sh
gst         # git status
gco         # git commit
gps         # git push (safe)
gpl         # git pull
gbr         # git branches
glg         # git log (pretty)
gfeat       # create feature branch
ghotfix     # create hotfix branch
gwip        # auto-commit WIP
gsync       # sync and cleanup
```

## 🎯 使用例

### 日常的な開発作業
```bash
# 朝の作業開始
gst                              # 状態確認
gpl                              # 最新を取得

# 新機能開発
gfeat "支払い機能"                # 機能ブランチ作成
# ... 開発作業 ...
gwip                             # WIP自動コミット
gco "支払い機能の基本実装完了"      # 正式コミット
gps                              # 安全プッシュ
```

### リリース作業
```bash
# リリース準備
./scripts/git-integration.sh release "v1.2.0"

# 健全性チェック
./scripts/git-integration.sh health

# 統計レポート
./scripts/git-integration.sh report
```

### 緊急対応
```bash
# ホットフィックス作成
ghotfix "緊急バグ修正"
# ... 修正作業 ...
gco "緊急バグ修正完了"
gps
```

## 🛡️ 安全機能

- **安全プッシュ**: リモートとの競合を事前チェック
- **WIP検出**: 作業中コミットの自動プッシュを防止
- **バックアップ**: 重要な作業の自動バックアップ
- **健全性チェック**: 機密情報や大きなファイルの検出

## 📊 レポート機能

- リポジトリ統計
- 貢献者情報
- ブランチ状態
- 最近のアクティビティ
- ファイル変更状況

## 🔍 トラブルシューティング

問題が発生した場合：

1. **権限確認**: `chmod +x scripts/git-*.sh`
2. **パス確認**: スクリプトが正しい場所にあることを確認
3. **Git状態確認**: `git status` で現在の状態を確認
4. **ヘルプ参照**: `./scripts/git-integration.sh help`

## 🔄 更新とメンテナンス

- 定期的な `git cleanup` でリポジトリを最適化
- `git health` で潜在的な問題を早期発見
- バックアップブランチの定期的な確認・削除

---

**作成日:** 2025年7月10日  
**最終更新:** 2025年7月10日
