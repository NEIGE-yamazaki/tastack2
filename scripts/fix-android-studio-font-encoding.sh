#!/bin/bash

# Android Studio 文字化け修正スクリプト

echo "=== Android Studio 文字化け修正 ==="
echo ""

# VMオプションファイルの確認と設定
VMOPTIONS_FILE="$HOME/.config/Google/AndroidStudio2023.3/studio64.vmoptions"

echo "1. VMオプション設定の確認と修正..."
if [ -f "$VMOPTIONS_FILE" ]; then
    echo "   ✓ VMオプションファイルが見つかりました: $VMOPTIONS_FILE"
    
    # 現在の設定を表示
    echo "   現在の設定:"
    cat "$VMOPTIONS_FILE" | grep -E "(encoding|language|country|aatext|SystemAAFont)" || echo "   関連設定が見つかりません"
    
    # 必要な設定が含まれているかチェック
    if grep -q "swing.aatext" "$VMOPTIONS_FILE"; then
        echo "   ✓ アンチエイリアス設定が含まれています"
    else
        echo "   ⚠ アンチエイリアス設定が不足しています"
    fi
else
    echo "   ✗ VMオプションファイルが見つかりません"
    echo "   Android Studioを一度起動してファイルを生成してください"
fi

echo ""
echo "2. インストール済み日本語フォントの確認..."
echo "   Noto CJK フォント:"
fc-list | grep "Noto.*CJK.*JP" | head -3 | while read line; do
    font_name=$(echo "$line" | sed 's/:.*$//' | xargs basename)
    echo "      ✓ $font_name"
done

echo "   Takao フォント:"
fc-list | grep -i takao | head -3 | while read line; do
    font_name=$(echo "$line" | sed 's/:.*$//' | xargs basename)
    echo "      ✓ $font_name"
done

echo ""
echo "3. 環境変数の確認..."
echo "   現在のロケール設定:"
echo "      LANG: $LANG"
echo "      LC_ALL: ${LC_ALL:-未設定}"
echo "      LC_CTYPE: ${LC_CTYPE:-未設定}"

echo ""
echo "4. Android Studio文字化け修正の推奨設定..."

echo ""
echo "=== 修正手順 ==="
echo ""
echo "▼ 手順1: Android Studioを日本語環境で起動"
echo "   コマンド: npm run android:studio:ja"
echo ""

echo "▼ 手順2: フォント設定の変更"
echo "   1. File > Settings (Ctrl+Alt+S)"
echo "   2. Editor > Font を選択"
echo "   3. Font を以下のいずれかに変更:"
echo "      - Noto Sans Mono CJK JP (推奨)"
echo "      - TakaoPGothic"
echo "      - DejaVu Sans Mono"
echo "   4. Size を 14 に設定"
echo "   5. Apply をクリック"
echo ""

echo "▼ 手順3: UI全体のフォント設定"
echo "   1. Appearance & Behavior > Appearance を選択"
echo "   2. Use custom font をチェック"
echo "   3. Font を 'Noto Sans CJK JP' に設定"
echo "   4. Size を 13 に設定"
echo "   5. Apply をクリック"
echo ""

echo "▼ 手順4: エンコーディング設定の確認"
echo "   1. Editor > File Encodings を選択"
echo "   2. 以下がすべて UTF-8 に設定されていることを確認:"
echo "      - Global Encoding"
echo "      - Project Encoding" 
echo "      - Default encoding for properties files"
echo ""

echo "▼ 手順5: Android Studioの再起動"
echo "   設定変更後、Android Studioを完全に再起動してください"
echo ""

echo "=== トラブルシューティング ==="
echo ""
echo "◆ 文字化けが続く場合:"
echo "   1. キャッシュクリア:"
echo "      rm -rf ~/.config/Google/AndroidStudio*/system/caches/"
echo "   2. Android Studio再起動"
echo ""

echo "◆ 特定の文字のみ化ける場合:"
echo "   1. Settings > Editor > Font"
echo "   2. Fallback font を 'DejaVu Sans Mono' に設定"
echo ""

echo "◆ ログ出力が化ける場合:"
echo "   1. android/gradle.properties に以下を追加:"
echo "      org.gradle.jvmargs=-Dfile.encoding=UTF-8"
echo ""

echo "=== テスト方法 ==="
echo ""
echo "文字化け修正の確認:"
echo "1. 新しいJavaファイルを作成"
echo "2. 以下のコメントを入力:"
echo "   // これは日本語のテストコメントです"
echo "   // ひらがな、カタカナ、漢字のテスト"
echo "3. 正しく表示されることを確認"
echo "4. Run/Debug ウィンドウで日本語ログが正しく表示されることを確認"
echo ""

echo "=== 完了 ==="
echo "このスクリプトの指示に従ってAndroid Studioの設定を変更してください。"
echo ""
