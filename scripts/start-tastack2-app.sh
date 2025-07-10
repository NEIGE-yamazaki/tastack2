#!/bin/bash

# TASTACK2アプリ起動・管理スクリプト
echo "📱 TASTACK2 アプリ管理"
echo "======================"

APP_PACKAGE="com.hintoru.tastack2"
MAIN_ACTIVITY="$APP_PACKAGE/.MainActivity"

# デバイス確認
echo "🔍 デバイス確認:"
devices=$(adb devices | grep -v "List of devices attached" | grep "device")
if [ -z "$devices" ]; then
    echo "❌ デバイスが接続されていません"
    echo "エミュレータを起動してください: tastack2 emulator"
    exit 1
fi

echo "✅ 接続デバイス:"
adb devices

# アプリインストール確認
echo ""
echo "📦 アプリインストール確認:"
installed=$(adb shell pm list packages | grep "$APP_PACKAGE")
if [ -z "$installed" ]; then
    echo "❌ TASTACK2アプリがインストールされていません"
    echo "🔧 アプリをインストールしますか？ (y/n)"
    read -p "選択: " install_app
    
    if [ "$install_app" = "y" ] || [ "$install_app" = "Y" ]; then
        echo "📱 アプリをインストール中..."
        cd /home/user/tastack2/android && ./gradlew installDebug
        
        # インストール確認
        installed=$(adb shell pm list packages | grep "$APP_PACKAGE")
        if [ -z "$installed" ]; then
            echo "❌ インストールに失敗しました"
            exit 1
        fi
        echo "✅ インストール完了"
    else
        echo "❌ アプリが必要です。終了します"
        exit 1
    fi
else
    echo "✅ TASTACK2アプリがインストールされています"
fi

# アプリ起動
echo ""
echo "🚀 TASTACK2アプリを起動中..."
adb shell am start -n "$MAIN_ACTIVITY"

# 起動確認
sleep 3
echo ""
echo "📊 アプリ状況確認:"

# プロセス確認
process=$(adb shell ps | grep "$APP_PACKAGE")
if [ -n "$process" ]; then
    echo "✅ アプリが実行中です"
    echo "$process"
else
    echo "⚠️  プロセスが見つかりません（起動中の可能性があります）"
fi

# アクティビティ確認
activity=$(adb shell dumpsys activity activities | grep -i tastack2)
if [ -n "$activity" ]; then
    echo "✅ アクティビティが実行中です"
else
    echo "ℹ️  アクティビティ情報を取得できませんでした"
fi

echo ""
echo "🎉 TASTACK2アプリの起動コマンドを実行しました"
echo ""
echo "📝 ログを確認する場合:"
echo "   tastack2 log"
echo ""
echo "🔄 アプリを再起動する場合:"
echo "   adb shell am start -n $MAIN_ACTIVITY"
