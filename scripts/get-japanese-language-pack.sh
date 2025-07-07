#!/bin/bash

# Android Studio日本語言語パック取得スクリプト

echo "=== Android Studio 日本語言語パック取得 ==="
echo ""

# プラグインディレクトリの準備
PLUGIN_DIR="$HOME/android-plugins"
mkdir -p "$PLUGIN_DIR"

echo "1. プラグインファイルの確認..."

# Windowsダウンロードフォルダから検索
echo "   Windowsダウンロードフォルダを検索中..."
WINDOWS_DOWNLOADS="/mnt/c/Users/user/Downloads"
if [ -d "$WINDOWS_DOWNLOADS" ]; then
    LANG_PACK_FILES=$(find "$WINDOWS_DOWNLOADS" -name "*japan*" -o -name "*language*pack*" -o -name "*jp*" 2>/dev/null | head -5)
    if [ -n "$LANG_PACK_FILES" ]; then
        echo "   ✓ 言語パックファイルが見つかりました:"
        echo "$LANG_PACK_FILES"
        
        # 最初に見つかったファイルをコピー
        FIRST_FILE=$(echo "$LANG_PACK_FILES" | head -1)
        cp "$FIRST_FILE" "$PLUGIN_DIR/"
        echo "   ✓ ファイルをコピーしました: $PLUGIN_DIR/$(basename "$FIRST_FILE")"
    else
        echo "   ✗ Windowsダウンロードフォルダに言語パックファイルが見つかりませんでした"
    fi
else
    echo "   ✗ Windowsダウンロードフォルダにアクセスできません"
fi

echo ""
echo "2. 代替ダウンロード方法..."

# 複数のダウンロード先を試す
DOWNLOAD_URLS=(
    "https://github.com/YiiGuxing/intellij-platform-plugin-language-pack-japanese/releases/latest/download/japanese-language-pack.zip"
    "https://plugins.jetbrains.com/plugin/download?rel=true&updateId=523725"
)

for url in "${DOWNLOAD_URLS[@]}"; do
    echo "   $url から試行中..."
    if curl -L -o "$PLUGIN_DIR/japanese-lang-pack-temp.zip" "$url" 2>/dev/null; then
        # ファイルサイズを確認（1KB以上なら有効と判断）
        if [ $(stat -c%s "$PLUGIN_DIR/japanese-lang-pack-temp.zip" 2>/dev/null || echo 0) -gt 1024 ]; then
            mv "$PLUGIN_DIR/japanese-lang-pack-temp.zip" "$PLUGIN_DIR/japanese-language-pack.zip"
            echo "   ✓ ダウンロード成功!"
            break
        else
            rm -f "$PLUGIN_DIR/japanese-lang-pack-temp.zip"
            echo "   ✗ ダウンロード失敗（ファイルサイズが小さすぎます）"
        fi
    else
        echo "   ✗ ダウンロード失敗"
    fi
done

echo ""
echo "3. 利用可能なプラグインファイル:"
ls -la "$PLUGIN_DIR/"

echo ""
echo "4. Android Studioでのインストール手順:"
echo "   a) Android Studioを起動"
echo "   b) File > Settings (Ctrl+Alt+S)"
echo "   c) Plugins を選択"
echo "   d) 設定アイコン（⚙️）> Install Plugin from Disk"
echo "   e) 以下のパスを入力またはファイルを選択:"

# 利用可能なファイルをリスト表示
for file in "$PLUGIN_DIR"/*.zip; do
    if [ -f "$file" ] && [ $(stat -c%s "$file" 2>/dev/null || echo 0) -gt 1024 ]; then
        echo "      $(realpath "$file")"
    fi
done

echo ""
echo "5. WSL環境でのWindowsパスアクセス:"
echo "   Android StudioのファイルブラウザでWindowsファイルにアクセスする場合:"
echo "   - C:\\Users\\user\\Downloads\\ → /mnt/c/Users/user/Downloads/"
echo "   - パス入力欄に直接入力: /mnt/c/Users/user/Downloads/japanese-language-pack.zip"

echo ""
echo "=== 完了 ==="
