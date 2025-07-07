#!/bin/bash

# Android アプリ実行スクリプト

echo "🚀 === tastack2 Android アプリ実行 ==="
echo ""

# プロジェクトディレクトリに移動
cd /home/user/tastack2

# 現在の状況確認
echo "📋 1. 環境確認中..."
echo "   プロジェクト: $(pwd)"
echo "   Node.js: $(node --version 2>/dev/null || echo '未インストール')"
echo "   Android SDK: ${ANDROID_HOME:-未設定}"
echo "   Java: $(java -version 2>&1 | head -1 | cut -d'"' -f2 || echo '未設定')"
echo ""

# Laravel開発サーバーの状況確認
echo "🌐 2. Laravel開発サーバー確認..."
if curl -s http://localhost:8081 > /dev/null; then
    echo "   ✅ Laravel開発サーバーが実行中 (http://localhost:8081)"
else
    echo "   ⚠️  Laravel開発サーバーが停止中"
    echo "   Laravel開発サーバーを起動しますか？ [y/N]"
    read -r start_laravel
    if [[ $start_laravel =~ ^[Yy]$ ]]; then
        echo "   Laravel開発サーバーを起動中..."
        npm run dev &
        sleep 5
    fi
fi
echo ""

# Webアセットのビルド
echo "🔨 3. Webアセットをビルド中..."
if npm run build:mobile; then
    echo "   ✅ Webアセットのビルド完了"
else
    echo "   ❌ Webアセットのビルドに失敗しました"
    exit 1
fi
echo ""

# Capacitor同期
echo "🔄 4. Capacitor同期中..."
if npx cap sync android; then
    echo "   ✅ Capacitor同期完了"
else
    echo "   ❌ Capacitor同期に失敗しました"
    exit 1
fi
echo ""

# Android接続デバイス確認
echo "📱 5. Androidデバイス確認..."
if command -v adb > /dev/null; then
    DEVICES=$(adb devices | grep -v "List of devices" | grep -v "^$" | wc -l)
    if [ "$DEVICES" -gt 0 ]; then
        echo "   ✅ 接続済みデバイス/エミュレータ数: $DEVICES"
        adb devices | grep -v "List of devices" | grep -v "^$" | while read device; do
            echo "      - $device"
        done
    else
        echo "   ⚠️  接続済みデバイス/エミュレータがありません"
        echo "      Android Studioでエミュレータを起動するか、実機を接続してください"
    fi
else
    echo "   ⚠️  adbコマンドが見つかりません"
fi
echo ""

# 実行方法の選択
echo "🎯 6. 実行方法を選択してください:"
echo "   1) Android Studioで開く（推奨）"
echo "   2) コマンドラインでビルド & インストール"
echo "   3) Capacitorで直接実行"
echo "   4) 終了"
echo ""
read -p "選択してください [1-4]: " choice

case $choice in
    1)
        echo "📱 Android Studioを起動中..."
        npm run android:studio:ja
        echo ""
        echo "🎉 Android Studioが起動しました！"
        echo "   次の手順:"
        echo "   1. プロジェクトの読み込み完了を待つ"
        echo "   2. デバイス/エミュレータを選択"
        echo "   3. 緑の▶️ボタンをクリックして実行"
        ;;
    2)
        echo "🔨 Androidアプリをビルド中..."
        cd android
        if ./gradlew assembleDebug; then
            echo "   ✅ ビルド完了"
            if adb devices | grep -q "device$"; then
                echo "📱 デバイスにインストール中..."
                if ./gradlew installDebug; then
                    echo "   ✅ インストール完了"
                    echo "   📱 デバイス上でアプリを手動で起動してください"
                else
                    echo "   ❌ インストールに失敗しました"
                fi
            else
                echo "   ⚠️  デバイスが接続されていません"
                echo "   ビルドされたAPK: android/app/build/outputs/apk/debug/app-debug.apk"
            fi
        else
            echo "   ❌ ビルドに失敗しました"
        fi
        ;;
    3)
        echo "🚀 Capacitorで直接実行中..."
        if npx cap run android; then
            echo "   ✅ 実行完了"
        else
            echo "   ❌ 実行に失敗しました"
            echo "   Android Studioまたはデバイス接続を確認してください"
        fi
        ;;
    4)
        echo "👋 実行を終了します"
        exit 0
        ;;
    *)
        echo "❌ 無効な選択です"
        exit 1
        ;;
esac

echo ""
echo "🎉 === 実行完了 ==="
echo ""
echo "📚 追加情報:"
echo "   - ログ確認: adb logcat | grep -i capacitor"
echo "   - Chrome DevTools: chrome://inspect"
echo "   - 詳細ガイド: docs/android-execution-guide.md"
echo ""
