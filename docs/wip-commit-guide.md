# WIPコミット管理ガイド

## WIPコミットとは

WIP（Work In Progress）コミットは、作業中の変更を一時的に保存するためのコミットです。完全ではないが、作業の進捗を保存したい場合に使用します。

## WIPコミットの使用場面

### 1. 作業の一時保存

- 作業を中断する必要がある場合
- 別のブランチでの作業が必要な場合
- 実験的な変更をバックアップしたい場合

### 2. 定期的なバックアップ

- 長時間の作業での進捗保存
- 複雑な変更の段階的な保存
- 作業の可視化

## WIPコミットの作成方法

### 基本的な作成方法

```bash
git add .
git commit -m "WIP: 作業中の機能を追加"
```

### タイムスタンプ付きで作成

```bash
git add .
git commit -m "WIP: $(date '+%Y-%m-%d %H:%M:%S') - 作業中の変更"
```

### 自動化スクリプトを使用

```bash
./scripts/git-automation.sh wip
```

## WIPコミットの管理

### 1. WIPコミットの確認
```bash
# WIPコミットのみを表示
git log --oneline --grep="WIP"

# 最新のWIPコミットを表示
git log --oneline -1 --grep="WIP"
```

### 2. WIPコミットの修正
```bash
# 最新のWIPコミットを修正
git add .
git commit --amend -m "正式なコミットメッセージ"
```

### 3. WIPコミットの分割
```bash
# WIPコミットを取り消して再度コミット
git reset HEAD~1
git add 特定のファイル
git commit -m "機能A: 実装完了"
git add 他のファイル
git commit -m "機能B: 実装完了"
```

## WIPコミットのベストプラクティス

### 1. 命名規則
- `WIP: 機能名 - 作業内容`
- `WIP: [日時] - 作業内容`
- `WIP: 実験的な変更 - 詳細`

### 2. WIPコミットの整理
```bash
# WIPコミットをsquashして整理
git rebase -i HEAD~3

# WIPコミットを正式なコミットに変更
git commit --amend -m "正式なコミットメッセージ"
```

### 3. プッシュ前の確認
```bash
# WIPコミットがないか確認
git log --oneline --grep="WIP"

# WIPコミットがある場合は修正してからプッシュ
```

## 自動化ツールの活用

### 1. 自動WIPコミット
```bash
# 定期的にWIPコミットを作成
./scripts/git-automation.sh wip

# 監視モードで自動コミット
./scripts/git-automation.sh watch
```

### 2. WIPコミットの自動整理
```bash
# プッシュ前にWIPコミットをチェック
./scripts/git-automation.sh push

# プロジェクトの健全性をチェック
./scripts/git-automation.sh health
```

## VSCodeタスクとの連携

### WIPコミット作成タスク
- `Ctrl+Shift+P` → `Tasks: Run Task`
- `Git: WIP Auto-commit`を選択

### WIPコミット管理タスク
- `Git: Check WIP Status`
- `Git: Cleanup WIP Commits`

## 注意点

### 1. WIPコミットの制限
- 共有ブランチにはWIPコミットをプッシュしない
- WIPコミットは一時的なものとして扱う
- 正式なコミットに変更してからプッシュ

### 2. チーム開発での配慮
- WIPコミットは個人ブランチでのみ使用
- プルリクエスト前にWIPコミットを整理
- コードレビューでWIPコミットは避ける

### 3. バックアップの重要性
- WIPコミットは作業の重要なバックアップ
- 定期的なWIPコミットで作業を保護
- 実験的な変更は必ずWIPコミットで保存

## トラブルシューティング

### WIPコミットが多すぎる場合
```bash
# インタラクティブリベースで整理
git rebase -i HEAD~10

# 複数のWIPコミットをsquash
```

### WIPコミットを間違えてプッシュした場合
```bash
# 最新のコミットを取り消し
git reset HEAD~1

# 正式なコミットメッセージで再コミット
git commit -m "正式なコミットメッセージ"

# 強制プッシュ（注意深く使用）
git push --force-with-lease
```

このガイドを参考に、効果的なWIPコミット管理を実践してください。
