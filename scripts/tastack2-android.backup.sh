#!/bin/bash

# TASTACK2プロジェクト用のディレクトリ自動切り替えスクリプト

TASTACK2_DIR="/home/user/tastack2"

# 環境変数の設定
export CAPACITOR_ANDROID_STUDIO_PATH=/snap/bin/android-studio
export ANDROID_HOME=/home/user/Android/Sdk
export PATH=$PATH:$ANDROID_HOME/cmdline-tools/latest/bin:$ANDROID_HOME/platform-tools

# プロジェクトディレクトリの確認
if [ ! -d "$TASTACK2_DIR" ]; then
    echo "❌ エラー: TASTACK2プロジェクトディレクトリが見つかりません"
    echo "パス: $TASTACK2_DIR"
    exit 1
fi

# 現在のディレクトリを確認
if [ "$PWD" != "$TASTACK2_DIR" ]; then
    echo "📁 プロジェクトディレクトリに移動中..."
    echo "現在: $PWD"
    echo "移動先: $TASTACK2_DIR"
    cd "$TASTACK2_DIR"
fi

# 引数に基づいてコマンドを実行
case "$1" in
    "studio"|"s"|"1")
        echo "🚀 Android Studio を起動中..."
        echo "環境変数を設定中..."
        export CAPACITOR_ANDROID_STUDIO_PATH=/snap/bin/android-studio
        echo "パス: $CAPACITOR_ANDROID_STUDIO_PATH"
        
        # Android Studioの存在確認
        if [ ! -f "$CAPACITOR_ANDROID_STUDIO_PATH" ]; then
            echo "❌ Android Studio が見つかりません: $CAPACITOR_ANDROID_STUDIO_PATH"
            echo "別のパスを試しています..."
            
            # 他のパスを試す
            if [ -f "/snap/bin/android-studio" ]; then
                export CAPACITOR_ANDROID_STUDIO_PATH="/snap/bin/android-studio"
                echo "✅ Snap版を使用: $CAPACITOR_ANDROID_STUDIO_PATH"
            elif [ -f "/opt/android-studio/bin/studio.sh" ]; then
                export CAPACITOR_ANDROID_STUDIO_PATH="/opt/android-studio/bin/studio.sh"
                echo "✅ 通常版を使用: $CAPACITOR_ANDROID_STUDIO_PATH"
            else
                echo "❌ Android Studio が見つかりません"
                exit 1
            fi
        fi
        
        npm run android:studio
        ;;
    "studio-safe"|"ss"|"2")
        echo "🚀 Android Studio を安全に起動中..."
        npm run android:studio:safe
        ;;
    "log"|"l"|"3")
        echo "📝 ログ監視を開始中..."
        npm run android:log
        ;;
    "log-quick"|"lq"|"4")
        echo "📝 クイックログを確認中..."
        npm run android:log:quick
        ;;
    "build"|"b"|"5")
        echo "🔨 APKをビルド中..."
        echo "📱 接続デバイス:"
        adb devices
        npm run android:build
        ;;
    "install"|"i"|"6")
        echo "📱 アプリをインストール中..."
        
        # デバイス確認
        devices=$(adb devices | grep -v "List of devices attached" | grep -c "device")
        if [ "$devices" -eq 0 ]; then
            echo "❌ 接続されているデバイスがありません"
            echo "🚀 エミュレータを起動しますか？ (y/n)"
            read -p "選択: " start_emulator
            
            if [ "$start_emulator" = "y" ] || [ "$start_emulator" = "Y" ]; then
                echo "📱 エミュレータを起動中..."
                $ANDROID_HOME/emulator/emulator -avd tastack2_emulator &
                echo "⏳ エミュレータの起動を待機中..."
                
                # エミュレータの起動を待機
                for i in {1..30}; do
                    sleep 2
                    if adb devices | grep -q "device"; then
                        echo "✅ エミュレータが起動しました"
                        break
                    fi
                    echo "待機中... ($i/30)"
                done
                
                # 再度デバイス確認
                if ! adb devices | grep -q "device"; then
                    echo "❌ エミュレータの起動に失敗しました"
                    exit 1
                fi
            else
                echo "❌ デバイスが必要です。インストールを中止します"
                exit 1
            fi
        fi
        
        echo "📱 接続デバイス:"
        adb devices
        npm run android:install
        ;;
    "devices"|"d"|"7")
        echo "📱 デバイスを確認中..."
        npm run android:devices
        ;;
    "emulator"|"e"|"8")
        echo "🖥️ エミュレータを起動中..."
        npm run android:emulator
        ;;
    "emulator-kill"|"kill"|"k"|"9")
        echo "⏹️ エミュレータを終了中..."
        
        # 接続されているエミュレータを確認
        devices=$(adb devices | grep -v "List of devices attached" | grep "emulator" | wc -l)
        if [ "$devices" -eq 0 ]; then
            echo "❌ 起動中のエミュレータが見つかりません"
        else
            echo "📱 起動中のエミュレータ:"
            adb devices | grep "emulator"
            
            # adb emu kill を試す
            echo "🔄 エミュレータを終了しています..."
            adb emu kill 2>/dev/null || echo "⚠️ adb emu kill が失敗しました"
            
            # プロセスを強制終了
            emulator_pids=$(pgrep -f "emulator")
            if [ -n "$emulator_pids" ]; then
                echo "� エミュレータプロセスを強制終了中..."
                echo "$emulator_pids" | xargs kill -9 2>/dev/null
            fi
            
            # 確認
            sleep 2
            remaining_devices=$(adb devices | grep -v "List of devices attached" | grep "emulator" | wc -l)
            if [ "$remaining_devices" -eq 0 ]; then
                echo "✅ エミュレータが正常に終了しました"
            else
                echo "⚠️ 一部のエミュレータプロセスが残っている可能性があります"
                adb devices
            fi
        fi
        ;;
    "full-run"|"dev"|"f"|"10")
        echo "🚀 完全開発フロー: エミュレータ起動 + ビルド + インストール + アプリ起動"
        echo "======================================================================="
        
        # Step 1: エミュレータ起動
        echo "📱 Step 1: エミュレータ確認・起動"
        devices=$(adb devices | grep -v "List of devices attached" | grep -c "device")
        if [ "$devices" -eq 0 ]; then
            echo "🚀 エミュレータを起動中..."
            $ANDROID_HOME/emulator/emulator -avd tastack2_emulator -no-snapshot-save -no-audio &
            echo "⏳ エミュレータの起動を待機中..."
            
            for i in {1..60}; do
                sleep 2
                if adb devices | grep -q "emulator.*device"; then
                    echo "✅ エミュレータが起動しました"
                    break
                fi
                echo "待機中... ($i/60)"
            done
            
            # 最終確認
            if ! adb devices | grep -q "emulator.*device"; then
                echo "❌ エミュレータの起動に失敗しました"
                exit 1
            fi
        else
            echo "✅ デバイスが既に接続されています"
        fi
        
        # Step 2: ビルド
        echo ""
        echo "🔨 Step 2: APKビルド"
        npm run build:mobile
        
        # Step 3: インストール
        echo ""
        echo "📱 Step 3: アプリインストール"
        npm run android:install
        
        # Step 4: アプリ起動
        echo ""
        echo "🚀 Step 4: アプリ起動"
        adb shell am start -n "com.hintoru.tastack2/com.hintoru.tastack2.MainActivity"
        
        # Step 5: 確認
        echo ""
        echo "🔍 Step 5: 起動確認"
        sleep 2
        app_running=$(adb shell "ps | grep tastack2" | wc -l)
        if [ "$app_running" -gt 0 ]; then
            echo "✅ アプリが正常に起動しました"
            echo "📱 プロセス情報:"
            adb shell "ps | grep tastack2"
        else
            echo "⚠️ アプリの起動確認ができませんでした"
        fi
        
        echo ""
        echo "🎉 完了！開発環境が準備できました"
        echo "📱 接続デバイス:"
        adb devices
        echo ""
        echo "🔧 便利なコマンド:"
        echo "  - ログ監視: $(basename $0) log"
        echo "  - アプリ再起動: $(basename $0) start"
        echo "  - エミュレータ終了: $(basename $0) kill"
        ;;
    "help"|"")
        echo "�🔧 TASTACK2 Android開発ヘルパー"
        echo "=================================="
        echo "使用方法: $(basename $0) [コマンド]"
        echo ""
        echo "利用可能なコマンド:"
        echo "  studio      - Android Studio起動"
        echo "  studio-safe - Android Studio安全起動"
        echo "  log         - ログ監視開始"
        echo "  log-quick   - クイックログ確認"
        echo "  build       - APKビルド"
        echo "  install     - アプリインストール"
        echo "  devices     - デバイス確認"
        echo "  emulator    - エミュレータ起動"
        echo "  kill        - エミュレータ終了"
        echo "  run         - エミュレータ起動 + ビルド + インストール"
        echo "  full-run    - 完全開発フロー (エミュレータ起動 + ビルド + インストール + アプリ起動)"
        echo "  dev         - full-runの短縮版"
        echo "  start       - アプリ起動"
        echo "  info        - 環境情報表示"
        echo "  help        - このヘルプを表示"
        echo ""
        echo "例: $(basename $0) studio"
        ;;
    "run")
        echo "🚀 完全開発フロー: エミュレータ起動 + ビルド + インストール"
        echo "=================================="
        
        # Step 1: エミュレータ起動
        echo "📱 Step 1: エミュレータ確認・起動"
        devices=$(adb devices | grep -v "List of devices attached" | grep -c "device")
        if [ "$devices" -eq 0 ]; then
            echo "🚀 エミュレータを起動中..."
            $ANDROID_HOME/emulator/emulator -avd tastack2_emulator &
            echo "⏳ エミュレータの起動を待機中..."
            
            for i in {1..30}; do
                sleep 2
                if adb devices | grep -q "device"; then
                    echo "✅ エミュレータが起動しました"
                    break
                fi
                echo "待機中... ($i/30)"
            done
        else
            echo "✅ デバイスが既に接続されています"
        fi
        
        # Step 2: ビルド
        echo ""
        echo "🔨 Step 2: APKビルド"
        npm run build:mobile
        
        # Step 3: インストール
        echo ""
        echo "📱 Step 3: アプリインストール"
        npm run android:install
        
        echo ""
        echo "🎉 完了！アプリがエミュレータにインストールされました"
        echo "📱 接続デバイス:"
        adb devices
        ;;
    "start"|"st"|"12")
        echo "🚀 TASTACK2アプリを起動中..."
        ./scripts/start-tastack2-app.sh
        ;;
    "info"|"in"|"13")
        echo "🔍 TASTACK2 環境情報"
        echo "=================================="
        echo "プロジェクトディレクトリ: $TASTACK2_DIR"
        echo "Android Studio パス: $CAPACITOR_ANDROID_STUDIO_PATH"
        echo "Android SDK: $ANDROID_HOME"
        echo "現在のディレクトリ: $PWD"
        echo ""
        echo "📱 接続デバイス:"
        adb devices
        echo ""
        echo "🚀 Android Studio 存在確認:"
        if [ -f "$CAPACITOR_ANDROID_STUDIO_PATH" ]; then
            echo "✅ 見つかりました: $CAPACITOR_ANDROID_STUDIO_PATH"
        else
            echo "❌ 見つかりません: $CAPACITOR_ANDROID_STUDIO_PATH"
        fi
        ;;
    "help"|"h"|""|"0")
        echo "🔧 TASTACK2 Android開発ヘルパー"
        echo "=================================="
        echo "使用方法: $(basename $0) [コマンド]"
        echo ""
        echo "利用可能なコマンド:"
        echo "  1/s/studio      - Android Studio起動"
        echo "  2/ss/studio-safe - Android Studio安全起動"
        echo "  3/l/log         - ログ監視開始"
        echo "  4/lq/log-quick  - クイックログ確認"
        echo "  5/b/build       - APKビルド"
        echo "  6/i/install     - アプリインストール"
        echo "  7/d/devices     - デバイス確認"
        echo "  8/e/emulator    - エミュレータ起動"
        echo "  9/k/kill        - エミュレータ終了"
        echo "  10/f/full-run   - 完全開発フロー (エミュレータ起動 + ビルド + インストール + アプリ起動)"
        echo "  /dev            - full-runの短縮版"
        echo "  11/r/run        - エミュレータ起動 + ビルド + インストール"
        echo "  12/st/start     - アプリ起動"
        echo "  13/in/info      - 環境情報表示"
        echo "  0/h/help        - このヘルプを表示"
        echo ""
        echo "例: $(basename $0) 1    # Android Studio起動"
        echo "    $(basename $0) f    # 完全開発フロー"
        echo "    $(basename $0) dev  # 完全開発フロー（短縮版）"
        ;;
    *)
        echo "❌ 不明なコマンド: $1"
        echo "ヘルプを表示するには: $(basename $0) help または $(basename $0) h または $(basename $0) 0"
        exit 1
        ;;
esac
