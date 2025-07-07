# Android Studio WSL環境でのC:\ドライブアクセス - 完全ガイド

## 現在の状況
- WSL (Windows Subsystem for Linux) 環境でAndroid Studioを使用
- プラグインの「Install Plugin from Disk」でC:\ドライブにアクセスしたい
- ファイルブラウザがWSLファイルシステムを表示している

## 解決方法

### ✅ 方法1: パス欄に直接入力（推奨）

Android Studioのファイルブラウザで：

1. **Settings** > **Plugins** > **⚙️ Settings icon** > **Install Plugin from Disk**
2. ファイルブラウザが開いたら、**パス欄**に直接入力：
   ```
   /mnt/c/Users/user/Downloads/
   ```
3. Enterキーを押してディレクトリに移動
4. 目的のプラグインファイル（.zip）を選択

### ✅ 方法2: WindowsのダウンロードフォルダをWSLからアクセス

#### パス変換表
| Windows パス | WSL パス |
|-------------|----------|
| `C:\Users\user\Downloads\` | `/mnt/c/Users/user/Downloads/` |
| `C:\Users\user\Desktop\` | `/mnt/c/Users/user/Desktop/` |
| `C:\temp\` | `/mnt/c/temp/` |

#### 実際の手順
```bash
# 1. ダウンロードフォルダの内容を確認
ls -la /mnt/c/Users/user/Downloads/

# 2. プラグインファイルを検索
find /mnt/c/Users/user/Downloads/ -name "*.zip" | grep -i japan

# 3. ファイルパスをコピーしてAndroid Studioで使用
```

### ✅ 方法3: ファイルをWSL環境にコピー

```bash
# 1. プラグイン用ディレクトリを作成
mkdir -p ~/Downloads

# 2. Windowsからファイルをコピー
cp "/mnt/c/Users/user/Downloads/japanese-language-pack.zip" ~/Downloads/

# 3. Android Studioで以下のパスを使用
# /home/user/Downloads/japanese-language-pack.zip
```

## 🔧 実際の操作手順

### 手順1: ファイルの確認
```bash
# 現在利用可能なプラグインファイルを確認
find /mnt/c/Users/user/Downloads/ -name "*.zip" -o -name "*.jar" | head -10
```

### 手順2: Android Studioでのプラグインインストール

1. Android Studioを起動:
   ```bash
   npm run android:studio:ja
   ```

2. **File** > **Settings** (または `Ctrl+Alt+S`)

3. **Plugins** を選択

4. **⚙️ Settings icon** > **Install Plugin from Disk**

5. ファイルブラウザで以下のいずれかを実行:
   
   **オプションA: パス直接入力**
   - パス欄に `/mnt/c/Users/user/Downloads/` と入力
   - Enterキーで移動
   
   **オプションB: ホームから移動**
   - パス欄に `/home/user/Downloads/` と入力
   - 事前にファイルをコピーしておく

6. プラグインファイル（.zip）を選択

7. **OK** をクリックして再起動

## 🚨 トラブルシューティング

### ケース1: /mnt/c/ が見つからない

```bash
# WSLマウント設定を確認
cat /etc/wsl.conf

# 設定ファイルが存在しない場合は作成
sudo tee /etc/wsl.conf > /dev/null <<EOF
[automount]
enabled = true
root = /mnt/
EOF

# WSLを再起動（PowerShellから）
# wsl --shutdown && wsl
```

### ケース2: 権限エラー

```bash
# ファイル権限を確認
ls -la /mnt/c/Users/user/Downloads/

# 必要に応じて読み取り権限を追加
chmod +r /mnt/c/Users/user/Downloads/*.zip
```

### ケース3: Android Studioがパスを認識しない

1. **絶対パスを使用**:
   ```
   /mnt/c/Users/user/Downloads/japanese-language-pack.zip
   ```

2. **ファイルブラウザのリフレッシュ**:
   - `F5` キーを押すか、フォルダアイコンをクリック

3. **ファイル拡張子の確認**:
   ```bash
   file /mnt/c/Users/user/Downloads/*.zip
   ```

## 📋 チェックリスト

### インストール前の確認
- [ ] WSLからWindowsファイルシステムにアクセス可能: `ls /mnt/c/`
- [ ] プラグインファイルが存在: `ls /mnt/c/Users/user/Downloads/*.zip`
- [ ] ファイルが有効なZIPファイル: `file filename.zip`

### インストール手順
- [ ] Android Studio起動: `npm run android:studio:ja`
- [ ] Settings > Plugins に移動
- [ ] Install Plugin from Disk を選択
- [ ] 正しいパスを入力: `/mnt/c/Users/user/Downloads/`
- [ ] プラグインファイルを選択
- [ ] Android Studioを再起動

### インストール後の確認
- [ ] Settings > Appearance & Behavior > System Settings で言語が日本語に設定
- [ ] メニューが日本語で表示される
- [ ] エラーメッセージが日本語で表示される

## 🔗 参考コマンド

```bash
# WSL環境の確認
wsl --version

# WindowsパスからWSLパスへの変換
wslpath -u "C:\Users\user\Downloads"

# WSLパスからWindowsパスへの変換
wslpath -w /home/user/Downloads

# ファイルの詳細情報
stat /mnt/c/Users/user/Downloads/*.zip

# Android Studioプロセスの確認
ps aux | grep studio
```

## 💡 Tips

1. **パス入力時のコツ**:
   - Tab補完を活用: `/mnt/c/Us[Tab]`
   - 大文字小文字を正確に入力

2. **ファイル管理**:
   - プラグインファイルは `~/android-plugins/` にまとめて保存
   - 不要になったファイルは定期的に削除

3. **効率的なワークフロー**:
   - VS Codeタスクから起動: `Ctrl+Shift+P` > `Android Studio (日本語)`
   - npm scriptsを活用: `npm run android:studio:ja`
