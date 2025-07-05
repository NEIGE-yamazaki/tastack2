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

# Android開発環境チェック（Windows）
echo "🤖 Android開発環境をチェック中..."
if [ -d "/c/Users/$USER/AppData/Local/Android/Sdk" ] || [ -d "/mnt/c/Users/$USER/AppData/Local/Android/Sdk" ]; then
    echo "✅ Android SDK が見つかりました"
    
    # Android プロジェクトのビルドテスト（任意）
    if [ -d "android" ]; then
        echo "🔧 Android プロジェクトの構文チェック中..."
        cd android
        if command -v ./gradlew &> /dev/null; then
            ./gradlew build --dry-run
            if [ $? -eq 0 ]; then
                echo "✅ Android プロジェクト構文チェック完了"
            else
                echo "⚠️ Android プロジェクトに問題がある可能性があります"
            fi
        fi
        cd ..
    fi
else
    echo "⚠️ Android SDK が見つかりません（Android Studio未インストール？）"
fi

# .envファイルの確認
echo "⚙️ 環境設定ファイルをチェック中..."
if [ ! -f ".env" ]; then
    echo "⚠️ .envファイルが見つかりません"
fi

echo "✅ すべてのチェックが完了しました！"
echo "📤 以下のコマンドでプッシュしてください:"
echo "    git push origin $(git branch --show-current)"
