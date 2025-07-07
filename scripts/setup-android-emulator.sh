#!/bin/bash

# Android エミュレータ自動セットアップスクリプト

echo "🚀 === Android エミュレータ セットアップ ==="
echo ""

# Android SDK の確認
if [ -z "$ANDROID_HOME" ]; then
    echo "❌ ANDROID_HOME が設定されていません"
    echo "   export ANDROID_HOME=/home/user/Android/Sdk"
    exit 1
fi

echo "📋 1. 環境確認中..."
echo "   ANDROID_HOME: $ANDROID_HOME"
echo "   cmdline-tools: $(ls -la $ANDROID_HOME/cmdline-tools/ 2>/dev/null | grep latest || echo '未インストール')"

# cmdline-tools の確認
SDKMANAGER="$ANDROID_HOME/cmdline-tools/latest/bin/sdkmanager"
AVDMANAGER="$ANDROID_HOME/cmdline-tools/latest/bin/avdmanager"

if [ ! -f "$SDKMANAGER" ]; then
    echo "❌ Android SDK Command Line Tools が見つかりません"
    echo "   Android Studio > SDK Manager > SDK Tools > Android SDK Command-line Tools をインストールしてください"
    exit 1
fi

# 既存のエミュレータを確認
echo ""
echo "📱 2. 既存のエミュレータを確認中..."
EXISTING_AVDS=$($AVDMANAGER list avd 2>/dev/null | grep "Name:" || echo "")

if [ -n "$EXISTING_AVDS" ]; then
    echo "   ✅ 既存のエミュレータが見つかりました:"
    echo "$EXISTING_AVDS" | sed 's/^/      /'
    echo ""
    echo "   既存のエミュレータを起動しますか？ [Y/n]"
    read -r start_existing
    if [[ ! $start_existing =~ ^[Nn]$ ]]; then
        FIRST_AVD=$(echo "$EXISTING_AVDS" | head -1 | sed 's/.*Name: *//')
        echo "   📲 エミュレータ '$FIRST_AVD' を起動中..."
        
        # バックグラウンドで起動
        $ANDROID_HOME/emulator/emulator @"$FIRST_AVD" > /dev/null 2>&1 &
        EMULATOR_PID=$!
        
        echo "   ⏳ エミュレータの起動を待機中（最大60秒）..."
        
        # エミュレータの起動を待つ
        for i in {1..30}; do
            if adb devices | grep -q "emulator.*device"; then
                echo "   ✅ エミュレータが正常に起動しました！"
                echo ""
                echo "🎉 === セットアップ完了 ==="
                echo "   接続済みデバイス:"
                adb devices | grep -v "List of devices"
                echo ""
                echo "次の手順:"
                echo "   1. npm run android:run"
                echo "   2. Android Studioでアプリを実行"
                exit 0
            fi
            echo "   待機中... ($i/30)"
            sleep 2
        done
        
        echo "   ⚠️  エミュレータの起動に時間がかかっています"
        echo "   バックグラウンドで起動中です（PID: $EMULATOR_PID）"
        echo "   'adb devices' で状況を確認してください"
        exit 0
    fi
fi

echo "   新しいエミュレータを作成します..."
echo ""

# システムイメージの確認・インストール
echo "📦 3. 必要なコンポーネントをインストール中..."

# 利用可能なシステムイメージを確認
echo "   利用可能なシステムイメージを確認中..."
AVAILABLE_IMAGES=$($SDKMANAGER --list 2>/dev/null | grep "system-images" | grep "google_apis" | grep "x86_64" | head -5)

if [ -z "$AVAILABLE_IMAGES" ]; then
    echo "   ❌ システムイメージが見つかりません"
    echo "   Android SDK を更新してください"
    exit 1
fi

echo "   利用可能なシステムイメージ:"
echo "$AVAILABLE_IMAGES" | sed 's/^/      /'

# 推奨システムイメージの選択
RECOMMENDED_IMAGE="system-images;android-30;google_apis;x86_64"
echo ""
echo "   推奨システムイメージ: $RECOMMENDED_IMAGE"
echo "   このシステムイメージをインストールしますか？ [Y/n]"
read -r install_image

if [[ ! $install_image =~ ^[Nn]$ ]]; then
    echo "   📥 システムイメージをダウンロード中..."
    if $SDKMANAGER "$RECOMMENDED_IMAGE"; then
        echo "   ✅ システムイメージのインストール完了"
    else
        echo "   ❌ システムイメージのインストールに失敗しました"
        exit 1
    fi
fi

# AVD の作成
echo ""
echo "🔧 4. エミュレータ（AVD）を作成中..."

AVD_NAME="Pixel_4_API_30_$(date +%s)"
echo "   AVD名: $AVD_NAME"

# AVD作成コマンド実行
if echo "no" | $AVDMANAGER create avd \
    -n "$AVD_NAME" \
    -k "$RECOMMENDED_IMAGE" \
    -d "pixel_4" \
    --force; then
    echo "   ✅ AVD の作成完了"
else
    echo "   ❌ AVD の作成に失敗しました"
    exit 1
fi

# エミュレータの起動
echo ""
echo "🚀 5. エミュレータを起動中..."
echo "   ⚠️  初回起動は5-10分程度かかる場合があります"

# エミュレータをバックグラウンドで起動
$ANDROID_HOME/emulator/emulator @"$AVD_NAME" \
    -gpu auto \
    -memory 2048 \
    -cores 2 \
    -accel on > /dev/null 2>&1 &

EMULATOR_PID=$!
echo "   エミュレータ起動中（PID: $EMULATOR_PID）..."

# 起動完了を待つ
echo "   ⏳ エミュレータの起動完了を待機中..."
for i in {1..60}; do
    if adb devices | grep -q "emulator.*device"; then
        echo "   ✅ エミュレータが正常に起動しました！"
        break
    fi
    
    if [ $((i % 10)) -eq 0 ]; then
        echo "   待機中... ($i/60秒)"
    fi
    sleep 1
done

# 最終確認
echo ""
echo "📱 6. 接続状況の確認..."
adb devices

if adb devices | grep -q "device$"; then
    echo ""
    echo "🎉 === セットアップ完了 ==="
    echo "   ✅ エミュレータが正常に起動しました"
    echo ""
    echo "次の手順:"
    echo "   1. npm run android:run     # 自動実行スクリプト"
    echo "   2. npm run android:build   # アプリをビルド"
    echo "   3. npm run android:install # アプリをインストール"
    echo ""
    echo "🔍 デバッグ:"
    echo "   - npm run android:logcat   # ログ確認"
    echo "   - adb devices             # デバイス確認"
    echo ""
else
    echo ""
    echo "⚠️  === エミュレータ起動中 ==="
    echo "   エミュレータはバックグラウンドで起動中です"
    echo "   完全に起動するまで数分お待ちください"
    echo ""
    echo "確認コマンド:"
    echo "   adb devices  # 接続状況確認"
    echo ""
    echo "起動完了後に以下を実行:"
    echo "   npm run android:run"
fi

echo ""
