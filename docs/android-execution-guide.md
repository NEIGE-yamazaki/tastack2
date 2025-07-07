# Android アプリ実行ガイド - tastack2

## 概要
tastack2 Laravel + Capacitor.js プロジェクトのAndroidアプリを実行する方法について説明します。

## 前提条件

### 必要なソフトウェア
- ✅ Android Studio (インストール済み)
- ✅ Android SDK & Build Tools (設定済み)
- ✅ Java 17 (設定済み)
- ✅ Node.js & npm (インストール済み)

### 環境変数確認
```bash
# 環境変数の確認
echo "ANDROID_HOME: $ANDROID_HOME"
echo "JAVA_HOME: $JAVA_HOME"
echo "PATH includes Android SDK: $(echo $PATH | grep android || echo 'Not found')"
```

## 実行方法

### 🚀 方法1: npmスクリプト（推奨）

#### デバッグビルド & 実行
```bash
# Webアセットをビルドしてアプリを起動
npm run android:build

# または段階的に実行
npm run build:mobile    # Webアセットのビルド
npm run android:studio  # Android Studioでプロジェクトを開く
```

#### リリースビルド
```bash
# リリース用APKをビルド
npm run android:release
```

### 🎯 方法2: Android Studio GUI

#### 手順
1. **Android Studioを起動**:
   ```bash
   npm run android:studio:ja  # 日本語環境で起動
   ```

2. **プロジェクトを開く**:
   - `Open an existing Android Studio project`
   - `/home/user/tastack2/android` フォルダを選択

3. **デバイスを選択**:
   - ツールバーのデバイス選択ドロップダウン
   - エミュレータまたは実機を選択

4. **実行**:
   - 緑の▶️ボタンをクリック
   - または `Shift + F10`

### 🔧 方法3: コマンドライン

#### Gradleコマンド直接実行
```bash
cd android

# デバッグビルド
./gradlew assembleDebug

# アプリをインストール & 実行（デバイス接続時）
./gradlew installDebug

# リリースビルド
./gradlew assembleRelease
```

#### Capacitorコマンド
```bash
# Webアセットを同期してAndroidビルド
npx cap sync android
npx cap build android

# Android Studioでプロジェクトを開く
npx cap open android

# デバイス上で直接実行
npx cap run android
```

## 実行環境の選択

### 📱 Android エミュレータ

#### エミュレータの作成・起動
```bash
# 利用可能なエミュレータを確認
$ANDROID_HOME/emulator/emulator -list-avds

# エミュレータを起動（例：Pixel_3a_API_30）
$ANDROID_HOME/emulator/emulator @Pixel_3a_API_30
```

#### Android Studio でエミュレータ管理
1. **Tools** > **AVD Manager**
2. **Create Virtual Device**
3. デバイス定義を選択（例：Pixel 4）
4. システムイメージを選択（API Level 30以上推奨）
5. **Finish** をクリック

### 📲 実機デバイス

#### USB デバッグ有効化
1. デバイスの **設定** > **デバイス情報**
2. **ビルド番号** を7回タップ（開発者オプション有効化）
3. **設定** > **開発者オプション**
4. **USB デバッグ** を有効化
5. USBケーブルでPCに接続

#### デバイス接続確認
```bash
# 接続済みデバイスを確認
adb devices

# 期待される出力例：
# List of devices attached
# emulator-5554    device
# AB1C234567890    device
```

## トラブルシューティング

### 🚨 一般的な問題と解決方法

#### 1. Gradle ビルドエラー
```bash
# Gradleラッパーの権限確認
chmod +x android/gradlew

# Gradleキャッシュをクリア
cd android && ./gradlew clean

# プロジェクトを再ビルド
./gradlew build
```

#### 2. Android SDK パスエラー
```bash
# local.propertiesファイルを確認
cat android/local.properties

# 内容例：
# sdk.dir=/home/user/Android/Sdk
```

#### 3. Java バージョンエラー
```bash
# 現在のJavaバージョン確認
java -version
javac -version

# JAVA_HOME確認
echo $JAVA_HOME

# Java 17が設定されていることを確認
```

#### 4. デバイス認識されない
```bash
# ADBサーバーを再起動
adb kill-server
adb start-server

# デバイスの再確認
adb devices
```

#### 5. Webアセットが反映されない
```bash
# Webアセットの強制更新
npm run build:mobile
npx cap sync android

# Capacitorキャッシュクリア
npx cap clean android
npx cap sync android
```

## デバッグ方法

### 🔍 ログ確認

#### Android Studio デバッガ
1. **View** > **Tool Windows** > **Logcat**
2. フィルターで `tastack2` または `Capacitor` を検索
3. ログレベル（Info, Warning, Error）を選択

#### コマンドラインでログ確認
```bash
# リアルタイムログ表示
adb logcat | grep -i capacitor

# 特定タグのログのみ表示
adb logcat -s "Capacitor"

# Webビューのログ確認
adb logcat | grep -i "chromium\|webview"
```

### 🌐 Web デバッグ

#### Chrome DevTools
1. Chromeで `chrome://inspect` を開く
2. **Devices** セクションで対象デバイスを確認
3. **inspect** をクリックしてDevToolsを開く
4. Web部分のデバッグが可能

## パフォーマンス最適化

### ⚡ ビルド時間短縮

#### Gradle 並列ビルド
```bash
# android/gradle.properties に追加
echo "org.gradle.parallel=true" >> android/gradle.properties
echo "org.gradle.daemon=true" >> android/gradle.properties
```

#### インクリメンタルビルド
```bash
# 変更部分のみビルド
cd android && ./gradlew assembleDebug --parallel
```

### 📦 APKサイズ最適化

#### ProGuard有効化（リリースビルド）
```gradle
// android/app/build.gradle
android {
    buildTypes {
        release {
            minifyEnabled true
            proguardFiles getDefaultProguardFile('proguard-android-optimize.txt'), 'proguard-rules.pro'
        }
    }
}
```

## 自動化スクリプト

### 🤖 ワンクリック実行

#### 完全ビルド & 実行
```bash
#!/bin/bash
# scripts/run-android.sh

echo "=== Android アプリ実行 ==="

# Webアセットビルド
echo "1. Webアセットをビルド中..."
npm run build:mobile

# Capacitor同期
echo "2. Capacitor同期中..."
npx cap sync android

# Gradleビルド
echo "3. Androidアプリをビルド中..."
cd android && ./gradlew assembleDebug

# Android Studio起動
echo "4. Android Studioを起動中..."
npm run android:studio:ja

echo "完了！Android Studioでアプリを実行してください。"
```

## VS Code タスク統合

### ⚙️ Tasks.json 設定
```json
{
    "label": "Android: Build & Run",
    "type": "shell",
    "command": "./scripts/run-android.sh",
    "group": "build",
    "presentation": {
        "echo": true,
        "reveal": "always",
        "focus": false,
        "panel": "shared"
    }
}
```

### 🎮 使用方法
1. `Ctrl + Shift + P`
2. `Tasks: Run Task`
3. `Android: Build & Run` を選択

## 参考リンク

- [Android Studio公式ドキュメント](https://developer.android.com/studio)
- [Capacitor Android ガイド](https://capacitorjs.com/docs/android)
- [Gradle ビルドガイド](https://docs.gradle.org/current/userguide/userguide.html)

## 注意事項

⚠️ **重要**:
- 初回ビルド時は時間がかかります（10-15分程度）
- デバイスによってはUSBドライバーのインストールが必要です
- エミュレータは多くのメモリを使用します（最低8GB RAM推奨）
- ウイルスソフトがビルドを遅くする場合があります
