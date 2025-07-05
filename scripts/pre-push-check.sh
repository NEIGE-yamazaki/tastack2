#!/bin/bash
# pre-push-check.sh - Windows用プッシュ前チェックスクリプト

echo "🔍 プッシュ前チェック実行中..."

# カレントディレクトリの確認
if [ ! -f "composer.json" ]; then
    echo "❌ Laravelプロジェクトのルートディレクトリで実行してください"
    exit 1
fi

# PHPの構文チェック
echo "🔧 PHP構文チェック中..."
find app -name "*.php" -exec php -l {} \; | grep -v "No syntax errors"
if [ $? -eq 0 ]; then
    echo "❌ PHP構文エラーが見つかりました"
    exit 1
fi

# JavaScript/TypeScriptのビルドテスト
echo "🏗️ JavaScriptビルドテスト実行中..."
npm run build
if [ $? -ne 0 ]; then
    echo "❌ JavaScriptビルドが失敗しました"
    exit 1
fi

# モバイル向けビルドテスト
echo "📱 モバイルビルドテスト実行中..."
npm run build:mobile
if [ $? -ne 0 ]; then
    echo "❌ モバイルビルドが失敗しました"
    exit 1
fi

# .envファイルの確認
echo "⚙️ 環境設定ファイルをチェック中..."
if [ ! -f ".env" ]; then
    echo "⚠️ .envファイルが見つかりません"
fi

echo "✅ すべてのチェックが完了しました！"
echo "📤 以下のコマンドでプッシュしてください:"
echo "    git push origin $(git branch --show-current)"
