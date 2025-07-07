#!/bin/bash

# Android Studio 日本語設定テストスクリプト

echo "=== Android Studio 日本語設定テスト ==="
echo ""

# 環境変数の確認
echo "1. 環境変数の確認:"
echo "   LANG: $LANG"
echo "   LC_ALL: $LC_ALL"
echo ""

# VMオプションファイルの確認
echo "2. VMオプションファイルの確認:"
VMOPTIONS_FILE="$HOME/.config/Google/AndroidStudio2023.3/studio64.vmoptions"
if [ -f "$VMOPTIONS_FILE" ]; then
    echo "   ファイル: $VMOPTIONS_FILE"
    echo "   内容:"
    cat "$VMOPTIONS_FILE" | grep -E "(language|country|encoding)" || echo "   日本語設定が見つかりません"
else
    echo "   VMオプションファイルが見つかりません"
fi
echo ""

# 日本語起動スクリプトの確認
echo "3. 日本語起動スクリプトの確認:"
SCRIPT_FILE="$HOME/bin/android-studio-ja"
if [ -f "$SCRIPT_FILE" ] && [ -x "$SCRIPT_FILE" ]; then
    echo "   ✓ スクリプトが利用可能: $SCRIPT_FILE"
else
    echo "   ✗ スクリプトが見つからないか実行権限がありません"
fi
echo ""

# Android Studioの存在確認
echo "4. Android Studioの確認:"
if command -v android-studio &> /dev/null; then
    echo "   ✓ android-studio コマンドが利用可能"
    android-studio --version 2>/dev/null | head -3
elif command -v /opt/android-studio/bin/studio.sh &> /dev/null; then
    echo "   ✓ Android Studio が /opt/android-studio にインストール済み"
elif command -v studio &> /dev/null; then
    echo "   ✓ studio コマンドが利用可能"
else
    echo "   ✗ Android Studio が見つかりません"
fi
echo ""

# 利用可能なコマンドの表示
echo "5. 利用可能なコマンド:"
echo "   標準起動: npm run android:studio"
echo "   日本語起動: npm run android:studio:ja"
echo "   または直接: ~/bin/android-studio-ja"
echo ""

# プロジェクトの確認
echo "6. tastack2プロジェクトの確認:"
if [ -f "/home/user/tastack2/android/build.gradle" ]; then
    echo "   ✓ Androidプロジェクトが準備済み"
else
    echo "   ✗ Androidプロジェクトが見つかりません"
    echo "     以下のコマンドでプロジェクトをセットアップしてください:"
    echo "     cd /home/user/tastack2 && npm run build:mobile"
fi
echo ""

echo "=== 次のステップ ==="
echo "1. 'npm run android:studio:ja' でAndroid Studioを日本語環境で起動"
echo "2. プラグインマーケットプレースで 'Japanese Language Pack' を検索・インストール"
echo "3. Android Studioを再起動"
echo "4. 設定 > 外観とテーマ > UI オプション で日本語が選択されていることを確認"
echo ""
