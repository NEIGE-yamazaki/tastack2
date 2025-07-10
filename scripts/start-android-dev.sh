#!/bin/bash

# TanStack2 Android エミュレータ開発環境起動スクリプト
# 使用方法: ./scripts/start-android-dev.sh

set -e

echo "🚀 TanStack2 Android 開発環境を起動中..."

# 色定義
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# 関数定義
show_status() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

show_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

show_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

show_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# 1. エミュレータの起動状態を確認
show_status "エミュレータの状態を確認中..."
EMULATOR_STATUS=$(adb devices | grep -v "List of devices attached" | grep -c "emulator" || echo "0")

if [ "$EMULATOR_STATUS" -eq 0 ]; then
    show_warning "エミュレータが起動していません。起動します..."
    
    # エミュレータのリストを取得
    EMULATOR_LIST=$($ANDROID_HOME/cmdline-tools/latest/bin/avdmanager list avd | grep "Name:" | head -1 | sed 's/.*Name: //')
    
    if [ -z "$EMULATOR_LIST" ]; then
        show_error "利用可能なエミュレータが見つかりません"
        exit 1
    fi
    
    show_status "エミュレータ '$EMULATOR_LIST' を起動中..."
    nohup $ANDROID_HOME/emulator/emulator -avd "$EMULATOR_LIST" -no-snapshot-save -no-audio > /dev/null 2>&1 &
    
    # エミュレータが起動するまで待機
    show_status "エミュレータの起動を待機中..."
    timeout=60
    while [ $timeout -gt 0 ]; do
        if adb devices | grep -q "emulator.*device"; then
            show_success "エミュレータが起動しました"
            break
        fi
        sleep 2
        timeout=$((timeout - 2))
    done
    
    if [ $timeout -le 0 ]; then
        show_error "エミュレータの起動がタイムアウトしました"
        exit 1
    fi
else
    show_success "エミュレータは既に起動しています"
fi

# 2. Laravel Sail の起動状態を確認
show_status "Laravel Sailの状態を確認中..."
if ! docker ps | grep -q "sail-8.0/app"; then
    show_warning "Laravel Sailが起動していません。起動します..."
    ./vendor/bin/sail up -d
    show_success "Laravel Sailが起動しました"
else
    show_success "Laravel Sailは既に起動しています"
fi

# 3. Vite開発サーバーの起動状態を確認
show_status "Vite開発サーバーの状態を確認中..."
if ! pgrep -f "vite" > /dev/null; then
    show_warning "Vite開発サーバーが起動していません。起動します..."
    nohup npm run dev > vite.log 2>&1 &
    sleep 3
    show_success "Vite開発サーバーが起動しました (http://localhost:5173)"
else
    show_success "Vite開発サーバーは既に起動しています"
fi

# 4. アプリのビルドと同期
show_status "アプリをビルドしています..."
npm run build:mobile

# 5. アプリのインストール
show_status "アプリをエミュレータにインストール中..."
cd android && ./gradlew installDebug

# 6. アプリの起動
show_status "アプリを起動中..."
adb shell am start -n "com.hintoru.tastack2/com.hintoru.tastack2.MainActivity"

# 7. 開発環境の情報を表示
echo ""
echo "=========================================="
echo "🎉 開発環境の準備が完了しました！"
echo "=========================================="
echo ""
echo "📱 エミュレータ: $(adb devices | grep emulator | cut -f1)"
echo "🌐 Laravel: http://localhost (Sail)"
echo "⚡ Vite: http://localhost:5173"
echo "📋 アプリ: com.hintoru.tastack2"
echo ""
echo "🔧 便利なコマンド:"
echo "  - ログ監視: npm run android:log"
echo "  - クイックログ: npm run android:log:quick"
echo "  - アプリ再起動: adb shell am start -n com.hintoru.tastack2/com.hintoru.tastack2.MainActivity"
echo "  - 開発サーバー再起動: npm run dev"
echo ""
echo "🚀 開発を開始してください！"
