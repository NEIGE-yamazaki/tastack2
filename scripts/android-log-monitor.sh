#!/bin/bash

# Capacitor用の最適化されたログ確認スクリプト
echo "🔍 Capacitor Log Monitor - 最適化版"
echo "=================================="

# デバイス確認
echo "📱 接続デバイス:"
adb devices

# ログフィルタリングオプション
echo ""
echo "ログフィルタリングオプション:"
echo "1) Capacitor関連のみ (推奨)"
echo "2) アプリ関連のみ"
echo "3) エラーのみ"
echo "4) 全ログ (重い)"
echo "5) カスタムフィルタ"

read -p "選択してください (1-5): " choice

case $choice in
    1)
        echo "🚀 Capacitorログを監視中..."
        adb logcat -v time | grep -iE "(capacitor|ionic|cordova|tastack2)" --color=always
        ;;
    2)
        echo "📱 アプリログを監視中..."
        adb logcat -v time | grep -iE "(com.hintoru.tastack2|tastack2)" --color=always
        ;;
    3)
        echo "❌ エラーログを監視中..."
        adb logcat -v time *:E --color=always
        ;;
    4)
        echo "📋 全ログを監視中..."
        adb logcat -v time --color=always
        ;;
    5)
        read -p "フィルタキーワードを入力: " filter
        echo "🔍 カスタムフィルタ ($filter) でログを監視中..."
        adb logcat -v time | grep -i "$filter" --color=always
        ;;
    *)
        echo "❌ 無効な選択です"
        exit 1
        ;;
esac
