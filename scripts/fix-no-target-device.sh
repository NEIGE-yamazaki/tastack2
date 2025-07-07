#!/bin/bash

# "No target device found" 緊急解決スクリプト

echo "⚠️  === No target device found エラー解決 ==="
echo ""

echo "🔍 1. 現在の状況確認..."

# デバイス接続確認
echo "   接続済みデバイス:"
if adb devices | grep -v "List of devices" | grep -q "device"; then
    adb devices | grep -v "List of devices"
    echo "   ✅ デバイスが接続されています"
    echo ""
    echo "解決方法: Android Studio またはコマンドラインから直接アプリを実行してください"
    echo "   npm run android:build"
    echo "   npm run android:install"
    exit 0
else
    echo "   ❌ デバイスが接続されていません"
fi

# 利用可能なエミュレータ確認
echo ""
echo "   利用可能なエミュレータ:"
if $ANDROID_HOME/cmdline-tools/latest/bin/avdmanager list avd 2>/dev/null | grep -q "Name:"; then
    $ANDROID_HOME/cmdline-tools/latest/bin/avdmanager list avd | grep "Name:" | sed 's/^/      /'
    EMULATOR_EXISTS=true
else
    echo "      エミュレータが見つかりません"
    EMULATOR_EXISTS=false
fi

echo ""
echo "🚀 2. 解決方法を選択してください:"

if [ "$EMULATOR_EXISTS" = true ]; then
    echo "   1) 既存のエミュレータを起動"
    echo "   2) 新しいエミュレータを作成・起動"
    echo "   3) Android Studioを開いて手動設定"
    echo "   4) 実機接続の手順を表示"
    echo ""
    read -p "選択してください [1-4]: " choice
else
    echo "   1) 新しいエミュレータを作成・起動"
    echo "   2) Android Studioを開いて手動設定"
    echo "   3) 実機接続の手順を表示"
    echo ""
    read -p "選択してください [1-3]: " choice
    # 選択肢を調整
    if [ "$choice" = "2" ]; then choice="3"; fi
    if [ "$choice" = "3" ]; then choice="4"; fi
    if [ "$choice" = "1" ]; then choice="2"; fi
fi

case $choice in
    1)
        echo ""
        echo "📱 既存のエミュレータを起動中..."
        FIRST_AVD=$($ANDROID_HOME/cmdline-tools/latest/bin/avdmanager list avd | grep "Name:" | head -1 | sed 's/.*Name: *//')
        echo "   起動するエミュレータ: $FIRST_AVD"
        
        $ANDROID_HOME/emulator/emulator @"$FIRST_AVD" > /dev/null 2>&1 &
        echo "   エミュレータを起動しました（バックグラウンド）"
        echo ""
        echo "⏳ 起動完了まで数分お待ちください..."
        echo "   確認コマンド: adb devices"
        ;;
    2)
        echo ""
        echo "🔧 新しいエミュレータを作成・起動します..."
        ./scripts/setup-android-emulator.sh
        ;;
    3)
        echo ""
        echo "🎯 Android Studioを起動中..."
        npm run android:studio:ja
        echo ""
        echo "Android Studio での手順:"
        echo "   1. Tools > AVD Manager"
        echo "   2. Create Virtual Device"
        echo "   3. デバイスを選択（推奨: Pixel 4）"
        echo "   4. システムイメージを選択（推奨: API 30以上）"
        echo "   5. Finish をクリック"
        echo "   6. ▶️ ボタンでエミュレータを起動"
        ;;
    4)
        echo ""
        echo "📱 実機デバイス接続手順:"
        echo ""
        echo "Android デバイスの設定:"
        echo "   1. 設定 > デバイス情報"
        echo "   2. ビルド番号を7回タップ（開発者オプション有効化）"
        echo "   3. 設定 > 開発者オプション"
        echo "   4. USB デバッグ を ON"
        echo "   5. USBケーブルでPCに接続"
        echo "   6. 「USBデバッグを許可しますか？」で OK"
        echo ""
        echo "接続確認:"
        echo "   adb devices"
        echo ""
        echo "トラブルシューティング:"
        echo "   adb kill-server && adb start-server"
        ;;
    *)
        echo "❌ 無効な選択です"
        exit 1
        ;;
esac

echo ""
echo "📝 追加情報:"
echo "   - デバイス確認: adb devices"
echo "   - エミュレータ一覧: npm run android:emulator:list"
echo "   - ログ確認: npm run android:logcat"
echo "   - 詳細ガイド: docs/android-no-target-device-fix.md"
echo ""
