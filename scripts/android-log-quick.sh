#!/bin/bash

# クイックログ確認 - 最後の50行のみ表示
echo "🔍 最新のCapacitorログ (最後の50行)"
echo "=================================="

# ログバッファをクリア
adb logcat -c

# 短時間待機
sleep 2

# 最新ログを表示
echo "📱 Capacitor関連の最新ログ:"
adb logcat -d -v time | grep -iE "(capacitor|ionic|cordova|tastack2)" | tail -50

echo ""
echo "❌ 最新のエラーログ:"
adb logcat -d -v time *:E | tail -20

echo ""
echo "ℹ️  リアルタイムログを開始するには:"
echo "   ./scripts/android-log-monitor.sh"
