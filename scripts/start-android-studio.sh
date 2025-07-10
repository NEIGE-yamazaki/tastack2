#!/bin/bash

# Android Studio起動スクリプト
echo "🚀 Android Studio 起動中..."
echo "=================================="

# 環境変数の設定
export CAPACITOR_ANDROID_STUDIO_PATH=/snap/bin/android-studio
export ANDROID_HOME=/home/user/Android/Sdk
export PATH=$PATH:$ANDROID_HOME/cmdline-tools/latest/bin:$ANDROID_HOME/platform-tools

# Android Studioの起動確認
if [ -f "$CAPACITOR_ANDROID_STUDIO_PATH" ]; then
    echo "✅ Android Studio が見つかりました: $CAPACITOR_ANDROID_STUDIO_PATH"
else
    echo "❌ Android Studio が見つかりません: $CAPACITOR_ANDROID_STUDIO_PATH"
    echo "利用可能な選択肢:"
    echo "1) Snap版: /snap/bin/android-studio"
    echo "2) 通常版: /opt/android-studio/bin/studio.sh"
    echo "3) カスタムパスを入力"
    read -p "選択してください (1-3): " choice
    
    case $choice in
        1)
            export CAPACITOR_ANDROID_STUDIO_PATH=/snap/bin/android-studio
            ;;
        2)
            export CAPACITOR_ANDROID_STUDIO_PATH=/opt/android-studio/bin/studio.sh
            ;;
        3)
            read -p "Android Studioのパスを入力: " custom_path
            export CAPACITOR_ANDROID_STUDIO_PATH="$custom_path"
            ;;
        *)
            echo "❌ 無効な選択です"
            exit 1
            ;;
    esac
fi

# プロジェクトディレクトリに移動
cd /home/user/tastack2

# Android Studioを起動
echo "🔧 Android Studio を起動中..."
echo "パス: $CAPACITOR_ANDROID_STUDIO_PATH"

npx cap open android

echo "✅ Android Studio の起動コマンドを実行しました"
echo "Android Studio が起動するまで少々お待ちください..."
