#!/bin/bash

# Android Studio WSL - C:\ドライブアクセステスト

echo "=== Android Studio WSL C:\ドライブアクセステスト ==="
echo ""

echo "1. WSL環境でのWindowsファイルシステムアクセス確認:"
echo "   /mnt/c/ アクセステスト..."
if [ -d "/mnt/c/" ]; then
    echo "   ✓ C:\ドライブにアクセス可能"
    echo "   利用可能なディレクトリ:"
    ls -la /mnt/c/ | grep "^d" | head -5
else
    echo "   ✗ C:\ドライブにアクセスできません"
fi

echo ""
echo "2. ユーザーディレクトリの確認:"
USER_DIR="/mnt/c/Users/user"
if [ -d "$USER_DIR" ]; then
    echo "   ✓ ユーザーディレクトリが見つかりました: $USER_DIR"
    echo "   サブディレクトリ:"
    ls -la "$USER_DIR" | grep "^d" | awk '{print "      " $9}' | head -5
else
    echo "   ✗ ユーザーディレクトリが見つかりません"
fi

echo ""
echo "3. ダウンロードフォルダの確認:"
DOWNLOADS_DIR="/mnt/c/Users/user/Downloads"
if [ -d "$DOWNLOADS_DIR" ]; then
    echo "   ✓ ダウンロードフォルダが見つかりました: $DOWNLOADS_DIR"
    
    # ZIPファイルを検索
    echo "   ZIPファイルの検索:"
    ZIP_FILES=$(find "$DOWNLOADS_DIR" -name "*.zip" 2>/dev/null | head -5)
    if [ -n "$ZIP_FILES" ]; then
        echo "$ZIP_FILES" | while read file; do
            echo "      $(basename "$file")"
        done
    else
        echo "      ZIPファイルが見つかりませんでした"
    fi
    
    # 最近のファイル（5個）
    echo "   最近のファイル（5個）:"
    ls -lat "$DOWNLOADS_DIR" | head -6 | tail -5 | awk '{print "      " $9 " (" $6 " " $7 " " $8 ")"}'
    
else
    echo "   ✗ ダウンロードフォルダが見つかりません"
fi

echo ""
echo "4. Android Studioで使用するパス:"
echo "   ダウンロードフォルダ: $DOWNLOADS_DIR"
echo "   パス入力例："
echo "   - ファイルブラウザのパス欄に入力: /mnt/c/Users/user/Downloads/"
echo "   - 特定ファイルを指定: /mnt/c/Users/user/Downloads/filename.zip"

echo ""
echo "5. パス変換例:"
echo "   Windows → WSL パス変換:"
echo "   C:\\Users\\user\\Downloads\\ → /mnt/c/Users/user/Downloads/"
echo "   C:\\temp\\ → /mnt/c/temp/"
echo "   C:\\Program Files\\ → /mnt/c/Program Files/"

echo ""
echo "6. コマンドライン確認:"
echo "   wslpathコマンドでパス変換:"
if command -v wslpath &> /dev/null; then
    echo "   ✓ wslpathが利用可能"
    echo "   Windows → WSL: wslpath -u 'C:\\Users\\user\\Downloads'"
    echo "   WSL → Windows: wslpath -w /home/user"
else
    echo "   ✗ wslpathコマンドが見つかりません"
fi

echo ""
echo "=== 次のステップ ==="
echo "1. 'npm run android:studio:ja' でAndroid Studioを起動"
echo "2. File > Settings > Plugins > Install Plugin from Disk"
echo "3. パス欄に '/mnt/c/Users/user/Downloads/' を入力"
echo "4. 目的のプラグインファイル（.zip）を選択"
echo "5. OKをクリックしてAndroid Studioを再起動"
echo ""
