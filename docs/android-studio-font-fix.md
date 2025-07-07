# Android Studio 文字化け解決ガイド

## 問題の状況
- Android Studioで日本語文字が文字化けしている
- メニュー、コメント、ログ出力などが正しく表示されない
- 豆腐（□）や記号で表示される

## 解決方法

### 方法1: フォント設定の変更（最も効果的）

#### 手順
1. Android Studioを起動
2. **File** > **Settings** (または `Ctrl+Alt+S`)
3. **Editor** > **Font** を選択
4. 以下の設定を変更：

**エディタフォント設定:**
```
Font: Noto Sans Mono CJK JP
Size: 14
Line height: 1.2
```

**代替フォント（上記が利用できない場合）:**
- `DejaVu Sans Mono`
- `Liberation Mono`
- `Ubuntu Mono`

5. **Apply** をクリック

#### UI全体のフォント設定
1. **Appearance & Behavior** > **Appearance** を選択
2. **Use custom font** をチェック
3. フォント設定：
```
Font: Noto Sans CJK JP
Size: 13
```

### 方法2: JVMオプションの設定

#### VMオプションファイルの編集
```bash
# VMオプションファイルの場所を確認
find ~/.config -name "*studio*.vmoptions" 2>/dev/null
```

#### 設定追加
VMオプションファイルに以下を追加：
```
-Dfile.encoding=UTF-8
-Dsun.jnu.encoding=UTF-8
-Duser.language=ja
-Duser.country=JP
-Dswing.aatext=true
-Dawt.useSystemAAFontSettings=on
-Dswing.defaultlaf=com.sun.java.swing.plaf.gtk.GTKLookAndFeel
```

### 方法3: システムロケールの設定

#### 環境変数の設定
```bash
# .bashrcに追加
echo 'export LANG=ja_JP.UTF-8' >> ~/.bashrc
echo 'export LC_ALL=ja_JP.UTF-8' >> ~/.bashrc
echo 'export LC_CTYPE=ja_JP.UTF-8' >> ~/.bashrc

# 設定を反映
source ~/.bashrc
```

#### Android Studio起動時の環境変数
```bash
# 日本語環境で起動
LANG=ja_JP.UTF-8 LC_ALL=ja_JP.UTF-8 android-studio
```

### 方法4: 日本語フォントのインストール

#### 追加フォントのインストール
```bash
# Google Noto フォントのインストール
sudo apt update
sudo apt install fonts-noto-cjk fonts-noto-cjk-extra

# 日本語フォントのインストール
sudo apt install fonts-takao fonts-noto-color-emoji

# フォントキャッシュの更新
fc-cache -fv
```

## 具体的な設定手順

### ステップ1: 現在の設定確認
```bash
# システムフォントの確認
fc-list | grep -i "noto\|cjk\|japanese"

# ロケール設定の確認
locale

# Android Studio設定ディレクトリの確認
ls -la ~/.config/Google/AndroidStudio*/
```

### ステップ2: Android Studio設定変更

#### フォント設定の変更
1. **Settings** > **Editor** > **Font**
2. **Font:** を `Noto Sans Mono CJK JP` に変更
3. **Fallback font:** を `DejaVu Sans Mono` に設定
4. **Enable font ligatures** をチェック（オプション）

#### カラースキームの調整
1. **Settings** > **Editor** > **Color Scheme**
2. **Scheme:** を `Darcula` または `IntelliJ Light` に設定
3. **Console Colors** で文字色を確認・調整

### ステップ3: IDE全体の設定

#### 外観設定
1. **Settings** > **Appearance & Behavior** > **Appearance**
2. **Theme:** を適切なテーマに設定
3. **Use custom font** をチェック
4. **Font:** を `Noto Sans CJK JP` に設定

#### プロジェクト設定
1. **Settings** > **Editor** > **File Encodings**
2. **Global Encoding:** を `UTF-8` に設定
3. **Project Encoding:** を `UTF-8` に設定
4. **Default encoding for properties files:** を `UTF-8` に設定

## トラブルシューティング

### ケース1: 設定変更後も文字化けが続く

#### 解決方法
```bash
# Android Studioの完全停止
pkill -f android-studio

# 設定キャッシュの削除
rm -rf ~/.config/Google/AndroidStudio*/system/caches/

# Android Studioの再起動
npm run android:studio:ja
```

### ケース2: 特定の文字のみ文字化け

#### フォールバックフォントの設定
1. **Settings** > **Editor** > **Font**
2. **Fallback font** を追加設定：
   - Primary: `Noto Sans Mono CJK JP`
   - Fallback: `DejaVu Sans Mono`
   - Secondary: `Liberation Mono`

### ケース3: ログ出力が文字化け

#### コンソール設定の変更
1. **Settings** > **Editor** > **General** > **Console**
2. **Use soft wraps in console** をチェック
3. **Override console cycle buffer size** を設定

#### Gradleログの文字化け対策
```gradle
// android/gradle.properties に追加
org.gradle.jvmargs=-Dfile.encoding=UTF-8 -Duser.language=ja -Duser.country=JP
```

## 検証方法

### 文字化け修正の確認
1. **新しいプロジェクトを作成**
2. **日本語コメントを入力**:
   ```java
   // これは日本語のコメントです
   // ひらがな、カタカナ、漢字のテスト
   public void テストメソッド() {
       System.out.println("日本語出力テスト");
   }
   ```
3. **ログ出力の確認**:
   - Run/Debug ウィンドウで日本語が正しく表示されることを確認

### フォント確認コマンド
```bash
# インストール済みフォントの確認
fc-list | grep -i "noto.*cjk"

# Android Studioプロセスの環境変数確認
ps aux | grep android-studio
cat /proc/$(pgrep -f android-studio)/environ | tr '\0' '\n' | grep -E "LANG|LC_"
```

## 推奨設定

### 最適なフォント組み合わせ
```
エディタフォント: Noto Sans Mono CJK JP (14pt)
UIフォント: Noto Sans CJK JP (13pt)
フォールバック: DejaVu Sans Mono
```

### 推奨JVMオプション
```
-Dfile.encoding=UTF-8
-Dsun.jnu.encoding=UTF-8
-Duser.language=ja
-Duser.country=JP
-Dswing.aatext=true
-Dawt.useSystemAAFontSettings=on
```

## 参考リンク
- [Android Studio Font Settings](https://developer.android.com/studio/intro/studio-config#fonts)
- [JetBrains Font Configuration](https://www.jetbrains.com/help/idea/configuring-colors-and-fonts.html)
- [Google Noto Fonts](https://fonts.google.com/noto)

## 注意事項
- 設定変更後は必ずAndroid Studioを再起動してください
- フォント変更は即座に反映されない場合があります
- プロジェクトごとに異なる設定が必要な場合があります
