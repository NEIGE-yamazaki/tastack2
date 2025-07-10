#!/bin/bash

# Google Play Console 用リリースAPK作成スクリプト

echo "🔧 Google Play Console 用リリースAPK作成を開始します..."

# 1. キーストアファイルの存在確認
KEYSTORE_FILE="android/app/my-release-key.keystore"

if [ ! -f "$KEYSTORE_FILE" ]; then
    echo "⚠️  キーストアファイルが見つかりません"
    echo "📝 キーストアを作成します..."
    
    # キーストア作成コマンドを表示
    echo "以下のコマンドを実行してキーストアを作成してください："
    echo "keytool -genkey -v -keystore android/app/my-release-key.keystore -alias tastack2_key -keyalg RSA -keysize 2048 -validity 10000"
    echo ""
    echo "⚠️  重要：パスワードとキーエイリアスを覚えておいてください"
    echo "推奨設定："
    echo "- Key alias: tastack2_key"
    echo "- Organization: hintoru"
    echo "- Country: JP"
    echo ""
    exit 1
fi

# 2. gradle.properties の設定確認
GRADLE_PROPS="android/gradle.properties"

if ! grep -q "MYAPP_RELEASE_STORE_FILE" "$GRADLE_PROPS"; then
    echo "📝 gradle.properties にリリース設定を追加します..."
    
    echo "" >> "$GRADLE_PROPS"
    echo "# Release signing configuration" >> "$GRADLE_PROPS"
    echo "MYAPP_RELEASE_STORE_FILE=my-release-key.keystore" >> "$GRADLE_PROPS"
    echo "MYAPP_RELEASE_KEY_ALIAS=tastack2_key" >> "$GRADLE_PROPS"
    echo "MYAPP_RELEASE_STORE_PASSWORD=YOUR_STORE_PASSWORD" >> "$GRADLE_PROPS"
    echo "MYAPP_RELEASE_KEY_PASSWORD=YOUR_KEY_PASSWORD" >> "$GRADLE_PROPS"
    
    echo "⚠️  gradle.properties にパスワードを設定してください"
    echo "ファイルパス: $GRADLE_PROPS"
    echo "YOUR_STORE_PASSWORD と YOUR_KEY_PASSWORD を実際のパスワードに置き換えてください"
    echo ""
fi

# 3. build.gradle の設定確認
BUILD_GRADLE="android/app/build.gradle"

if ! grep -q "signingConfigs" "$BUILD_GRADLE"; then
    echo "📝 build.gradle にリリース署名設定を追加する必要があります"
    echo "手動で以下の設定を追加してください："
    echo ""
    echo "android {"
    echo "    signingConfigs {"
    echo "        release {"
    echo "            storeFile file(MYAPP_RELEASE_STORE_FILE)"
    echo "            storePassword MYAPP_RELEASE_STORE_PASSWORD"
    echo "            keyAlias MYAPP_RELEASE_KEY_ALIAS"
    echo "            keyPassword MYAPP_RELEASE_KEY_PASSWORD"
    echo "        }"
    echo "    }"
    echo "    buildTypes {"
    echo "        release {"
    echo "            signingConfig signingConfigs.release"
    echo "            minifyEnabled false"
    echo "            proguardFiles getDefaultProguardFile('proguard-android.txt'), 'proguard-rules.pro'"
    echo "        }"
    echo "    }"
    echo "}"
    echo ""
fi

# 4. 本番環境設定に切り替え
echo "🔧 本番環境設定に切り替えます..."
if [ -f "scripts/switch-capacitor-config.sh" ]; then
    ./scripts/switch-capacitor-config.sh production
else
    echo "⚠️  本番環境設定スクリプトが見つかりません"
fi

# 5. ビルド前のクリーンアップ
echo "🧹 ビルド前のクリーンアップ..."
cd android
./gradlew clean

# 6. リリースAPKのビルド
echo "🏗️  リリースAPKをビルドします..."
cd ..
npm run build:android:release

# 7. APKファイルの確認
APK_PATH="android/app/build/outputs/apk/release/app-release.apk"

if [ -f "$APK_PATH" ]; then
    echo "✅ リリースAPKが正常に作成されました！"
    echo "📁 APKファイル: $APK_PATH"
    echo "📊 ファイルサイズ: $(du -h "$APK_PATH" | cut -f1)"
    echo ""
    echo "🚀 次のステップ："
    echo "1. Google Play Console にアクセス"
    echo "2. 新しいアプリを作成"
    echo "3. $APK_PATH をアップロード"
    echo "4. ストア掲載情報を入力"
    echo "5. 審査提出"
else
    echo "❌ リリースAPKの作成に失敗しました"
    echo "エラーログを確認してください"
fi

echo ""
echo "📖 詳細な手順は docs/google-play-registration-guide.md を参照してください"
