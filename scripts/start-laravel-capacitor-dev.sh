#!/bin/bash

# TanStack2 Laravel Sail + Capacitor 統合開発環境起動スクリプト
# Laravel Sail (localhost:8081) + Capacitor (エミュレータから10.0.2.2:8081)

set -e

echo "🚀 TanStack2 Laravel Sail + Capacitor 開発環境を起動中..."

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

# 1. Laravel Sailの起動状態を確認
show_status "Laravel Sailの状態を確認中..."
if ! docker ps | grep -q "tastack2-laravel.test-1"; then
    show_warning "Laravel Sailが起動していません。起動します..."
    ./vendor/bin/sail up -d
    
    # Laravel Sailが起動するまで待機
    show_status "Laravel Sailの起動を待機中..."
    timeout=30
    while [ $timeout -gt 0 ]; do
        if docker ps | grep -q "tastack2-laravel.test-1"; then
            show_success "Laravel Sailが起動しました"
            break
        fi
        sleep 2
        timeout=$((timeout - 2))
    done
    
    if [ $timeout -le 0 ]; then
        show_error "Laravel Sailの起動がタイムアウトしました"
        exit 1
    fi
else
    show_success "Laravel Sailは既に起動しています"
fi

# 2. Laravel Sail (port 8081) の動作確認
show_status "Laravel Sail (localhost:8081) の動作確認中..."
if curl -s -f -o /dev/null http://localhost:8081; then
    show_success "Laravel Sail (localhost:8081) が正常に動作しています"
else
    show_error "Laravel Sail (localhost:8081) にアクセスできません"
    exit 1
fi

# 3. エミュレータの起動状態を確認
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

# 4. エミュレータから10.0.2.2:8081への接続テスト
show_status "エミュレータから10.0.2.2:8081への接続テスト中..."
if adb shell "ping -c 1 10.0.2.2" > /dev/null 2>&1; then
    show_success "エミュレータから10.0.2.2への基本接続が成功"
    
    # HTTPアクセステスト
    if adb shell "echo 'GET / HTTP/1.1\r\nHost: 10.0.2.2:8081\r\nConnection: close\r\n\r\n' | nc 10.0.2.2 8081" | grep -q "HTTP/1.1 200 OK"; then
        show_success "エミュレータから10.0.2.2:8081への HTTP接続が成功"
    else
        show_warning "エミュレータから10.0.2.2:8081への HTTP接続に問題があります"
    fi
else
    show_error "エミュレータから10.0.2.2への接続に失敗しました"
    exit 1
fi

# 5. Capacitor設定をエミュレータ用に確認・設定
show_status "Capacitor設定を確認中..."
if grep -q "http://10.0.2.2:8081" capacitor.config.json; then
    show_success "Capacitor設定は既にエミュレータ用 (10.0.2.2:8081) に設定されています"
else
    show_warning "Capacitor設定をエミュレータ用に変更します..."
    ./scripts/switch-capacitor-config.sh emulator
fi

# 6. アプリのビルドと同期
show_status "Capacitorアプリをビルド・同期中..."
npx cap sync

# 7. アプリのインストール
show_status "アプリをエミュレータにインストール中..."
cd android && ./gradlew installDebug

# 8. アプリの起動
show_status "アプリを起動中..."
adb shell am start -n "com.hintoru.tastack2/com.hintoru.tastack2.MainActivity"

# 9. 最終確認
echo ""
echo "=========================================="
echo "🎉 Laravel Sail + Capacitor開発環境の準備が完了しました！"
echo "=========================================="
echo ""
echo "📱 エミュレータ: $(adb devices | grep emulator | cut -f1)"
echo "🌐 Laravel Sail: http://localhost:8081"
echo "🔗 エミュレータ→Laravel: http://10.0.2.2:8081"
echo "📋 アプリ: com.hintoru.tastack2"
echo ""
echo "🔧 便利なコマンド:"
echo "  - ログ監視: npm run android:log"
echo "  - クイックログ: npm run android:log:quick"
echo "  - アプリ再起動: adb shell am start -n com.hintoru.tastack2/com.hintoru.tastack2.MainActivity"
echo "  - Laravel Sail停止: ./vendor/bin/sail down"
echo "  - エミュレータ終了: ./scripts/tastack2-android.sh kill"
echo ""
echo "🚀 開発を開始してください！"
