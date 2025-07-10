# VSCode Git ソース管理 修復ガイド

## 問題の概要

VSCodeのソース管理（Git）が動作しない場合の解決方法を説明します。

## 解決手順

### 1. 基本的な確認

1. **Gitリポジトリの状態確認**

   ```bash
   git status
   git log --oneline -5
   ```

2. **VSCodeの再起動**
   - `Ctrl+Shift+P` → `Developer: Reload Window`
   - または VSCode を完全に再起動

### 2. VSCode設定の確認

VSCodeの設定ファイル（`.vscode/settings.json`）に以下が含まれているか確認：

```json
{
  "git.enabled": true,
  "git.autoRepositoryDetection": "openEditors",
  "git.path": "git",
  "git.detectSubmodules": true,
  "scm.defaultViewMode": "tree",
  "scm.alwaysShowProviders": true,
  "scm.repositories.visible": 10
}
```

### 3. 利用可能なVSCodeタスク

以下のタスクを使用して問題を解決できます：

- **VSCode: Git ソース管理修復** - 自動診断と修復
- **VSCode: Git リポジトリ状態確認** - 現在の状態を確認
- **VSCode: Git 設定確認** - Git設定を確認

### 4. 手動での解決方法

1. **Git拡張機能の再有効化**
   - `Ctrl+Shift+X` → "Git" を検索
   - 無効化 → 有効化

2. **ソース管理パネルの更新**
   - `Ctrl+Shift+G` でソース管理パネルを開く
   - 更新ボタンをクリック

3. **フォルダーの再オープン**
   - `File` → `Open Folder` → プロジェクトフォルダーを選択

### 5. コマンドパレットでの操作

`Ctrl+Shift+P` で以下のコマンドを実行：

- `Git: Initialize Repository` - リポジトリの初期化
- `Git: Refresh` - Git情報の更新
- `Source Control: Refresh` - ソース管理の更新

## 一般的な問題と解決策

### 問題1: ソース管理パネルが空

**解決策:**

- VSCodeを再起動
- フォルダーを再度開く
- Git拡張機能を再有効化

### 問題2: 変更が表示されない

**解決策:**

- `git status` でターミナルから確認
- VSCodeの設定で `git.autoRepositoryDetection` を確認
- `.git` フォルダーのパーミッションを確認

### 問題3: リモートリポジトリに接続できない

**解決策:**

- `git remote -v` でリモートURLを確認
- 認証情報を確認
- ネットワーク接続を確認

## 予防策

1. **定期的なVSCode更新**
2. **Git拡張機能の最新化**
3. **設定ファイルのバックアップ**
4. **ワークスペース設定の統一**

## 追加リソース

- [VSCode Git 統合ドキュメント](https://code.visualstudio.com/docs/sourcecontrol/overview)
- [Git 公式ドキュメント](https://git-scm.com/doc)

---

**作成日:** 2025年7月10日  
**最終更新:** 2025年7月10日
