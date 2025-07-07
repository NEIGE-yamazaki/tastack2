#!/bin/bash

# Android デバイス接続 簡単解決ガイド

echo "🚀 === Android デバイス接続解決 ==="
echo ""

# 現在の状況確認
echo "📋 現在の状況:"
adb devices

if adb devices | grep -v "List of devices" | grep -q "device"; then
    echo "✅ デバイスが接続されています！"
    echo ""
    echo "🎯 次の手順:"
    echo "   npm run android:build    # アプリをビルド"
    echo "   npm run android:install  # デバイスにインストール"
    exit 0
fi

echo "❌ デバイスが接続されていません"
echo ""

# 解決方法の提示
echo "🔧 解決方法:"
echo ""

echo "【方法1】Android Studio でエミュレータを作成（推奨）"
echo "   1. npm run android:studio:ja"
echo "   2. Tools > AVD Manager"
echo "   3. Create Virtual Device"
echo "   4. Pixel 4 を選択 > Next"
echo "   5. API 30以上を選択 > Next > Finish"
echo "   6. ▶️ ボタンでエミュレータを起動"
echo ""

echo "【方法2】実機デバイスを接続"
echo "   Android デバイス側:"
echo "   1. 設定 > デバイス情報 > ビルド番号を7回タップ"
echo "   2. 設定 > 開発者オプション > USB デバッグ ON"
echo "   3. USBケーブルでPCに接続"
echo "   4. 「USBデバッグを許可」で OK"
echo ""

echo "【方法3】コマンドラインでエミュレータ作成"
echo "   ./scripts/setup-android-emulator.sh"
echo ""

echo "🔍 確認コマンド:"
echo "   adb devices              # デバイス接続確認"
echo "   adb kill-server && adb start-server  # ADB再起動"
echo ""

echo "📱 Android Studio を起動しますか？ [Y/n]"
read -r start_studio

if [[ ! $start_studio =~ ^[Nn]$ ]]; then
    echo "🎯 Android Studio を起動中..."
    npm run android:studio:ja
    echo ""
    echo "Android Studio が起動しました！"
    echo "AVD Manager でエミュレータを作成してください。"
fi

echo ""
echo "✨ 完了後の手順:"
echo "   1. adb devices で接続確認"
echo "   2. npm run android:run でアプリ実行"
echo ""
