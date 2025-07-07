# Android Studio 日本語化ガイド

## 概要
Android Studio Jellyfish (2023.3.1) での日本語言語パック設定について説明します。

## 方法1: JetBrains公式言語パック（推奨）

### 手順
1. Android Studioを起動
2. **File** > **Settings** (または **Ctrl+Alt+S**)
3. **Plugins** を選択
4. **Marketplace** タブをクリック
5. 検索ボックスに以下のキーワードを入力して検索：
   - `Japanese Language Pack`
   - `日本語`
   - `Language Pack`
   - `Locale`
   - `JetBrains Language Pack`

### 検索が見つからない場合の対処法

#### A. プラグイン設定の確認
1. **Settings** > **HTTP Proxy**
2. **No proxy** が選択されていることを確認
3. **Check connection** ボタンをクリックしてネットワーク接続を確認

#### B. IDE更新
1. **Help** > **Check for Updates**
2. 利用可能な更新があれば適用
3. 再起動後に再度プラグインを検索

#### C. 手動インストール
1. [JetBrains Plugin Repository](https://plugins.jetbrains.com/)にアクセス
2. "Japanese Language Pack"を検索
3. `.zip`ファイルをダウンロード
4. **Settings** > **Plugins** > **Settings icon** > **Install Plugin from Disk**
5. ダウンロードした`.zip`ファイルを選択

## 方法2: システムロケール設定

### Linux環境での日本語ロケール設定
```bash
# システムロケールを確認
locale

# 日本語ロケールの生成（Ubuntuの場合）
sudo locale-gen ja_JP.UTF-8

# ロケールの更新
sudo update-locale LANG=ja_JP.UTF-8

# 環境変数の設定
export LANG=ja_JP.UTF-8
export LC_ALL=ja_JP.UTF-8
```

### Android Studio起動時の言語設定
```bash
# 日本語環境でAndroid Studioを起動
LANG=ja_JP.UTF-8 android-studio
```

### IDE VMオプションの設定
1. **Help** > **Edit Custom VM Options**
2. 以下の行を追加：
```
-Duser.language=ja
-Duser.country=JP
-Dfile.encoding=UTF-8
```

## 方法3: 代替案

### A. IntelliJ IDEA Community Edition + Android Plugin
```bash
# IntelliJ IDEA Community Editionをインストール
sudo snap install intellij-idea-community --classic

# または
flatpak install flathub com.jetbrains.IntelliJ-IDEA-Community
```

### B. 日本語フォント設定
1. **Settings** > **Editor** > **Font**
2. **Font:** を日本語対応フォント（例：Noto Sans CJK JP）に変更
3. **Size:** を適切なサイズに調整

## トラブルシューティング

### プラグインが見つからない場合
1. **Settings** > **Plugins** > **Browse repositories**
2. リポジトリURLを手動追加：
   ```
   https://plugins.jetbrains.com
   ```

### 言語パックが機能しない場合
1. Android Studioを完全に終了
2. 設定ディレクトリをクリア：
   ```bash
   rm -rf ~/.config/Google/AndroidStudio*
   ```
3. Android Studioを再起動

### VMオプションでの日本語設定
```bash
# Android Studio起動スクリプトの作成
cat > ~/bin/android-studio-ja << 'EOF'
#!/bin/bash
export LANG=ja_JP.UTF-8
export LC_ALL=ja_JP.UTF-8
android-studio \
  -Duser.language=ja \
  -Duser.country=JP \
  -Dfile.encoding=UTF-8 \
  "$@"
EOF

chmod +x ~/bin/android-studio-ja
```

## 確認方法

### 言語設定の確認
1. Android Studioを起動
2. **Help** > **About** でバージョン情報を確認
3. メニューが日本語で表示されることを確認

### プロジェクトでの日本語サポート確認
1. Tastack2プロジェクトを開く
2. 日本語コメントやリソースが正しく表示されることを確認
3. デバッグ出力で日本語が正しく処理されることを確認

## 参考リンク
- [JetBrains Plugin Repository](https://plugins.jetbrains.com/)
- [Android Studio Settings Guide](https://developer.android.com/studio/intro/studio-config)
- [IntelliJ IDEA Localization](https://www.jetbrains.com/help/idea/supported-languages.html)

## 注意事項
- 言語パックのインストール後は必ずAndroid Studioを再起動してください
- 一部の機能やエラーメッセージは英語のままの場合があります
- プラグインの更新は定期的に行ってください
