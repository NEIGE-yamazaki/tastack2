#!/bin/bash
# setup-android-windows.sh - Windows環境でのAndroid開発セットアップ

echo "🤖 Windows Android開発環境セットアップ開始"

# 前提条件チェック
echo "📋 前提条件をチェック中..."

# Android Studio インストールチェック
ANDROID_STUDIO_PATHS=(
    "/c/Program Files/Android/Android Studio"
    "/c/Users/$USER/AppData/Local/Android Studio"
    "/mnt/c/Program Files/Android/Android Studio"
)

ANDROID_STUDIO_FOUND=false
for path in "${ANDROID_STUDIO_PATHS[@]}"; do
    if [ -d "$path" ]; then
        echo "✅ Android Studio が見つかりました: $path"
        ANDROID_STUDIO_FOUND=true
        break
    fi
done

if [ "$ANDROID_STUDIO_FOUND" = false ]; then
    echo "❌ Android Studio が見つかりません"
    echo "📥 Android Studio をインストールしてください: https://developer.android.com/studio"
    exit 1
fi

# Android SDK チェック
ANDROID_SDK_PATHS=(
    "/c/Users/$USER/AppData/Local/Android/Sdk"
    "/mnt/c/Users/$USER/AppData/Local/Android/Sdk"
    "/c/Android/Sdk"
)

ANDROID_SDK_FOUND=false
for path in "${ANDROID_SDK_PATHS[@]}"; do
    if [ -d "$path" ]; then
        echo "✅ Android SDK が見つかりました: $path"
        ANDROID_SDK_PATH="$path"
        ANDROID_SDK_FOUND=true
        break
    fi
done

if [ "$ANDROID_SDK_FOUND" = false ]; then
    echo "⚠️ Android SDK が見つかりません"
    echo "📋 Android Studio で SDK をインストールしてください"
fi

# Java JDK チェック
if command -v java &> /dev/null; then
    JAVA_VERSION=$(java -version 2>&1 | head -n 1 | cut -d'"' -f2)
    echo "✅ Java が見つかりました: $JAVA_VERSION"
else
    echo "⚠️ Java が見つかりません"
    echo "📋 Android Studio に含まれるJDKを使用してください"
fi

# Node.js & npm チェック
if command -v node &> /dev/null; then
    NODE_VERSION=$(node --version)
    echo "✅ Node.js が見つかりました: $NODE_VERSION"
else
    echo "❌ Node.js が見つかりません"
    echo "📥 Node.js をインストールしてください: https://nodejs.org/"
    exit 1
fi

# Capacitor CLI チェック
if command -v cap &> /dev/null; then
    CAP_VERSION=$(cap --version)
    echo "✅ Capacitor CLI が見つかりました: $CAP_VERSION"
else
    echo "📦 Capacitor CLI をインストール中..."
    npm install -g @capacitor/cli
fi

# プロジェクトセットアップ
echo "🔧 プロジェクト環境をセットアップ中..."

# 依存関係インストール
npm install

# Windows用環境設定
echo "⚙️ Windows用環境設定を適用中..."
cp .env.windows.example .env

# Capacitor プラットフォーム追加（未追加の場合）
if [ ! -d "android" ]; then
    echo "📱 Android プラットフォームを追加中..."
    npx cap add android
fi

# モバイル向けビルド
echo "🏗️ モバイル向けビルドを実行中..."
npm run build:mobile

echo "🎉 Windows Android開発環境セットアップ完了！"

echo "📋 次のステップ:"
echo "1. VS Code でプロジェクトを開く: code ."
echo "2. Android Studio でプロジェクトを開く: npm run cap:open:android"
echo "3. Android デバイスまたはエミュレータでテスト"
echo "4. APK ビルド: npm run build:android"

echo ""
echo "🔧 有用なコマンド:"
echo "  - Android Studio を開く: npm run cap:open:android"
echo "  - APK をビルド: npm run build:android"
echo "  - プッシュ前チェック: npm run sync:windows"
echo "  - Capacitor 同期: npm run build:mobile"
