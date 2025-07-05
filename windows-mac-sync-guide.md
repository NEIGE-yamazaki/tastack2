# Windows ↔ Mac 開発同期ガイド

## 事前準備

### Gitの設定統一
```bash
# 両環境で同じユーザー情報を設定
git config --global user.name "Your Name"
git config --global user.email "your.email@example.com"

# 改行コードの自動変換を無効化（重要）
git config --global core.autocrlf false

# VS Code をデフォルトエディタに設定
git config --global core.editor "code --wait"
```

### SSH キーの共有（推奨）
```bash
# Windowsで生成したSSHキーをMacでも使用
# または各環境で個別にSSHキーを設定してGitHubに登録
```

## Git ベースの同期（推奨）

### Windows での作業フロー
```bash
# 環境セットアップ（初回のみ）
npm run setup:windows

# Android開発環境セットアップ（初回のみ）
bash scripts/setup-android-windows.sh

# 開発開始前に最新を取得
git pull origin main

# 開発作業
npm run dev  # Web開発サーバー
npm run cap:open:android  # Android Studio起動

# Android APK ビルド
npm run build:android  # デバッグ版
npm run build:android:release  # リリース版

# プッシュ前チェック＆プッシュ
npm run sync:windows  # 自動チェック実行
git add .
git commit -m "新機能: モバイル機能追加"
git push origin main
```

### Mac での同期フロー
```bash
# 最新コードを取得
git pull origin main

# 依存関係更新（package.jsonが変更された場合）
npm install

# Capacitorプラグインの更新（必要に応じて）
npx cap sync

# iOS向けビルド
npm run build:mobile

# iOSでテスト
npx cap open ios
```

## 自動化スクリプト例

### Windows用: プッシュ前チェックスクリプト
```bash
#!/bin/bash
# pre-push-check.sh

echo "🔍 プッシュ前チェック実行中..."

# テスト実行
echo "🧪 テスト実行中..."
npm test

# Lintチェック
echo "🔧 コード品質チェック中..."
npm run lint

# ビルドテスト
echo "🏗️ ビルドテスト実行中..."
npm run build:mobile

echo "✅ すべてのチェックが完了しました！"
echo "📤 git push origin main でプッシュしてください"
```

### Mac用: 同期＆iOS開発スクリプト
```bash
#!/bin/bash
# sync-and-build-ios.sh

echo "🔄 Windows からの変更を同期中..."
git pull origin main

echo "📦 依存関係を更新中..."
npm install

echo "🔄 Capacitorプラグイン同期中..."
npx cap sync ios

echo "🏗️ モバイル向けビルド実行中..."
npm run build:mobile

echo "📱 iOS Simulator で起動中..."
npx cap run ios

echo "✅ iOS開発環境準備完了！"
```

## VS Code設定の同期

### ワークスペース設定ファイル作成
```json
// .vscode/settings.json
{
  "files.eol": "\n",
  "editor.insertFinalNewline": true,
  "editor.trimAutoWhitespace": true,
  "typescript.preferences.importModuleSpecifier": "relative",
  "emmet.includeLanguages": {
    "blade": "html"
  },
  "files.associations": {
    "*.blade.php": "blade"
  }
}
```

### 推奨拡張機能リスト
```json
// .vscode/extensions.json
{
  "recommendations": [
    "ms-vscode.vscode-typescript-next",
    "bradlc.vscode-tailwindcss",
    "onecentlin.laravel-blade",
    "ms-vscode.sublime-keybindings",
    "esbenp.prettier-vscode"
  ]
}
```

## 環境変数の同期

### .env ファイルの調整
```env
# 共通設定
APP_NAME="TanStack2 App"
APP_ENV=local
APP_DEBUG=true
APP_TIMEZONE="Asia/Tokyo"
APP_LOCALE=ja

# Windows環境用
APP_URL=http://localhost:8081
DB_HOST=127.0.0.1

# Mac環境用（必要に応じて調整）
APP_URL=http://localhost:8081
DB_HOST=127.0.0.1

# iOS開発用の追加設定
CAPACITOR_SERVER_URL=http://localhost:8081
```

### 環境別設定の管理
```bash
# .env.example をベースにした環境別ファイル
cp .env.example .env.windows
cp .env.example .env.mac

# シンボリックリンクまたはコピーで切り替え
# Windows: copy .env.windows .env
# Mac: cp .env.mac .env
```

## ブランチ戦略

### 開発フロー
```bash
# feature ブランチでの開発
git checkout -b feature/new-mobile-feature

# 開発・テスト
git add .
git commit -m "feat: 新しいモバイル機能を追加"

# Windows → Mac へのブランチ共有
git push origin feature/new-mobile-feature

# Mac側でブランチ取得
git fetch origin
git checkout feature/new-mobile-feature

# iOS でテスト後、main へマージ
git checkout main
git merge feature/new-mobile-feature
git push origin main
```

## トラブルシューティング

### よくある問題と解決策

#### 1. 改行コードの問題
```bash
# 全ファイルの改行コードを統一
git config --global core.autocrlf false
git rm --cached -r .
git reset --hard
```

#### 2. node_modules の同期問題
```bash
# Mac側で必ず再インストール
rm -rf node_modules package-lock.json
npm install
```

#### 3. Capacitor プラグインの同期問題
```bash
# プラグインを再同期
npx cap clean
npx cap sync
```

#### 4. iOS Simulator の問題
```bash
# シミュレータデータをリセット
xcrun simctl erase all
```

## パフォーマンス最適化

### Git の最適化
```bash
# 大きなファイルの除外設定
echo "node_modules/" >> .gitignore
echo "vendor/" >> .gitignore
echo "*.log" >> .gitignore
echo ".DS_Store" >> .gitignore
echo "Thumbs.db" >> .gitignore
```

### ビルド時間の短縮
```bash
# 並列ビルドの活用
npm run build:mobile -- --parallel

# キャッシュの活用
npm ci  # package-lock.json ベースの高速インストール
```
