# WSL環境でAndroid Studio - Windowsファイルシステムアクセスガイド

## 概要
WSL (Windows Subsystem for Linux) 環境でAndroid Studioを使用している場合、プラグインファイルをWindowsファイルシステム（C:\）からアクセスする方法について説明します。

## 問題の状況
- Android StudioでプラグインをDiscからインストールしようとする
- ファイルブラウザがWSLファイルシステム（/home/user/...）を表示
- Windowsのダウンロードフォルダ（C:\Users\...）にアクセスしたい

## 解決方法

### 方法1: Windowsパスを直接入力

#### 手順
1. Android Studioで **Settings** > **Plugins** > **Settings icon** > **Install Plugin from Disk**
2. ファイルブラウザのパス欄に直接Windowsパスを入力：
   ```
   /mnt/c/Users/[ユーザー名]/Downloads/
   ```

#### パス変換例
```bash
# Windowsパス → WSLパス変換
C:\Users\username\Downloads\plugin.zip
↓
/mnt/c/Users/username/Downloads/plugin.zip
```

### 方法2: wslpathコマンドを使用

#### Windowsパスの確認
```bash
# 現在のWindowsユーザー名を取得
wslpath -w ~
# 例出力: C:\Users\username

# Downloadsフォルダのパスを確認
wslpath -u "C:\Users\$USER\Downloads"
# 例出力: /mnt/c/Users/username/Downloads
```

#### プラグインファイルの場所確認
```bash
# Windowsのダウンロードフォルダ内のプラグインファイルを検索
find /mnt/c/Users/*/Downloads -name "*.zip" -o -name "*.jar" 2>/dev/null | grep -i japan
```

### 方法3: ファイルをWSL環境にコピー

#### 手順
```bash
# Downloadsフォルダを確認
ls /mnt/c/Users/*/Downloads/*.zip 2>/dev/null

# プラグインファイルをWSL環境にコピー
cp "/mnt/c/Users/[ユーザー名]/Downloads/japanese-language-pack.zip" ~/Downloads/

# Android Studioから ~/Downloads/ にアクセス
```

### 方法4: Windows Explorer統合を使用

#### 手順
1. Windows Explorerで目的のフォルダを開く
2. アドレスバーに `\\wsl$\Ubuntu\home\user\` と入力
3. ファイルをWSL環境のホームディレクトリにコピー
4. Android Studioのファイルブラウザからアクセス

## 具体的なコマンド例

### 日本語言語パックのダウンロード・インストール

```bash
# 作業ディレクトリを作成
mkdir -p ~/android-plugins

# Windowsのダウンロードフォルダから検索
find /mnt/c/Users -name "*japan*" -o -name "*language*pack*" 2>/dev/null

# 見つかったファイルをコピー（例）
cp "/mnt/c/Users/username/Downloads/japanese-language-pack.zip" ~/android-plugins/

# Android Studioで使用するパスを表示
echo "Android Studioで使用するパス: $(realpath ~/android-plugins/)"
```

### WSL環境の設定確認

```bash
# WSLバージョンの確認
wsl --version

# マウント設定の確認
cat /proc/mounts | grep drvfs

# Windowsドライブの確認
ls -la /mnt/
```

## トラブルシューティング

### ケース1: /mnt/c/ にアクセスできない

```bash
# WSL設定ファイルの確認
cat /etc/wsl.conf

# 設定ファイルが存在しない場合は作成
sudo tee /etc/wsl.conf > /dev/null <<EOF
[automount]
enabled = true
root = /mnt/
options = "metadata,umask=22,fmask=11"
mountFsTab = false

[interop]
enabled = true
appendWindowsPath = true
EOF

# WSLを再起動（PowerShellから実行）
# wsl --shutdown
# wsl
```

### ケース2: ファイルアクセス権限の問題

```bash
# ファイルの権限を確認
ls -la /mnt/c/Users/*/Downloads/

# 必要に応じて権限を変更
chmod 644 ~/android-plugins/*.zip
```

### ケース3: Android Studioでパスが認識されない

1. **フルパスを使用**:
   ```
   /home/user/android-plugins/japanese-language-pack.zip
   ```

2. **ファイルブラウザで手動移動**:
   - ホームディレクトリ（~）から開始
   - android-pluginsフォルダに移動
   - プラグインファイルを選択

## 推奨ワークフロー

### プラグインインストール手順

1. **ダウンロード確認**:
   ```bash
   ls /mnt/c/Users/*/Downloads/*japan*.zip
   ```

2. **WSL環境にコピー**:
   ```bash
   cp "/mnt/c/Users/[username]/Downloads/japanese-language-pack.zip" ~/
   ```

3. **Android Studioでインストール**:
   - **Settings** > **Plugins** > **Install Plugin from Disk**
   - パス: `/home/user/japanese-language-pack.zip`

4. **クリーンアップ**:
   ```bash
   rm ~/japanese-language-pack.zip
   ```

## 参考リンク
- [WSL ファイルシステムアクセス](https://docs.microsoft.com/ja-jp/windows/wsl/filesystems)
- [JetBrains プラグインリポジトリ](https://plugins.jetbrains.com/)

## 注意事項
- WSL環境では大文字小文字を区別します
- Windowsパスのバックスラッシュ（\）をスラッシュ（/）に変更してください
- ユーザー名にスペースが含まれる場合は引用符で囲んでください
