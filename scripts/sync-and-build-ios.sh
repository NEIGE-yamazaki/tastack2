#!/bin/bash
# sync-and-build-ios.sh - Mac用同期＆iOS開発スクリプト

echo "🔄 Windows からの変更を同期中..."

# Gitの最新状態を取得
git fetch origin

# 現在のブランチを確認
CURRENT_BRANCH=$(git branch --show-current)
echo "📍 現在のブランチ: $CURRENT_BRANCH"

# pullの実行
git pull origin $CURRENT_BRANCH
if [ $? -ne 0 ]; then
    echo "❌ git pull が失敗しました。コンフリクトを解決してください"
    exit 1
fi

# package.jsonの変更確認
if git diff HEAD~1 --name-only | grep -q "package.json"; then
    echo "📦 package.jsonが更新されました。依存関係を再インストール中..."
    npm install
fi

# Capacitorプラグインの同期
echo "🔄 Capacitorプラグイン同期中..."
npx cap sync ios
if [ $? -ne 0 ]; then
    echo "❌ Capacitor同期が失敗しました"
    exit 1
fi

# モバイル向けビルド
echo "🏗️ モバイル向けビルド実行中..."
npm run build:mobile
if [ $? -ne 0 ]; then
    echo "❌ モバイルビルドが失敗しました"
    exit 1
fi

# iOS Simulatorの確認
echo "📱 iOS Simulatorの状況確認中..."
xcrun simctl list devices | grep "Booted" > /dev/null
if [ $? -eq 0 ]; then
    echo "✅ iOS Simulatorが起動済みです"
else
    echo "⚠️ iOS Simulatorが起動していません"
fi

# iOS開発環境の起動
echo "🚀 iOS開発環境を起動中..."
npx cap open ios

echo "✅ iOS開発環境準備完了！"
echo "📝 次の手順:"
echo "    1. Xcodeでプロジェクトを確認"
echo "    2. シミュレータまたは実機でテスト"
echo "    3. 問題があればGitで報告"
