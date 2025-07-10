# Google Play Console アプリ登録ガイド

## 事前準備

### 1. リリース用APKの作成

#### 署名キーの作成

```bash
# キーストアの作成
keytool -genkey -v -keystore my-release-key.keystore -alias alias_name -keyalg RSA -keysize 2048 -validity 10000

# キーストアをandroid/app/ディレクトリに移動
mv my-release-key.keystore android/app/
```

#### gradle.properties の設定

`android/gradle.properties` に以下を追加：

```
MYAPP_RELEASE_STORE_FILE=my-release-key.keystore
MYAPP_RELEASE_KEY_ALIAS=alias_name
MYAPP_RELEASE_STORE_PASSWORD=パスワード
MYAPP_RELEASE_KEY_PASSWORD=パスワード
```

#### build.gradle の設定

`android/app/build.gradle` のsigningConfigs セクションを追加：

```gradle
android {
    ...
    signingConfigs {
        release {
            storeFile file(MYAPP_RELEASE_STORE_FILE)
            storePassword MYAPP_RELEASE_STORE_PASSWORD
            keyAlias MYAPP_RELEASE_KEY_ALIAS
            keyPassword MYAPP_RELEASE_KEY_PASSWORD
        }
    }
    buildTypes {
        release {
            signingConfig signingConfigs.release
            minifyEnabled false
            proguardFiles getDefaultProguardFile('proguard-android.txt'), 'proguard-rules.pro'
        }
    }
}
```

### 2. リリースAPKのビルド

```bash
# 現在のプロジェクトの場合
npm run build:android:release

# または直接
cd android
./gradlew assembleRelease
```

生成されるAPKパス：
`android/app/build/outputs/apk/release/app-release.apk`

## Google Play Console での登録

### 1. 新しいアプリの作成

1. **Play Console にログイン**
   - <https://play.google.com/console>

2. **「アプリを作成」をクリック**

3. **アプリ詳細の入力**
   - アプリ名：`TASTACK2`
   - デフォルトの言語：日本語
   - アプリまたはゲーム：アプリ
   - 有料または無料：無料（または有料）

4. **宣言事項**
   - アプリのコンテンツに関する宣言
   - 開発者プログラムポリシーへの同意

### 2. アプリバンドルのアップロード

1. **「リリース」→「本番環境」を選択**

2. **「新しいリリースを作成」をクリック**

3. **APKまたはApp Bundleをアップロード**
   - 先ほど作成したリリースAPKをアップロード
   - `android/app/build/outputs/apk/release/app-release.apk`

### 3. ストア掲載情報の設定

1. **アプリの詳細**
   - アプリ名：TASTACK2
   - 簡単な説明：（アプリの概要を2-3行で）
   - 詳細な説明：（アプリの機能を詳しく説明）

2. **グラフィック素材**
   - アイコン：512x512 PNG
   - 機能グラフィック：1024x500 PNG
   - スクリーンショット：最低2枚必要
     - 携帯電話用：320-3840px（幅）x 320-3840px（高さ）

3. **分類**
   - アプリのカテゴリを選択
   - コンテンツレーティングを設定

### 4. コンテンツレーティング

1. **「コンテンツレーティング」セクション**
2. **アンケートに回答**
   - アプリの内容に関する質問
   - 暴力、性的内容、薬物使用等について

### 5. 対象ユーザーとコンテンツ

1. **対象年齢層を選択**
2. **子供向けかどうかを選択**
3. **プライバシーポリシーのURL**（必須）

### 6. アプリのコンテンツ

1. **データセーフティ**
   - ユーザーデータの収集と使用について
   - データの共有について

2. **広告**
   - 広告の有無
   - 広告の種類

3. **アプリ内購入**
   - 有料コンテンツの有無

## 現在のプロジェクト設定

- **アプリID**: `com.hintoru.tastack2`
- **アプリ名**: `TASTACK2`
- **バージョンコード**: 1
- **バージョン名**: 1.0

## 注意事項

1. **テスト環境の設定**
   - 内部テストまたはクローズドテストを先に実行することを推奨
   - 本番環境にリリースする前にテストを実行

2. **審査時間**
   - 初回審査は数時間〜数日かかる場合があります
   - 更新の審査は通常数時間以内

3. **必要な書類**
   - プライバシーポリシー（必須）
   - 利用規約（推奨）

4. **スクリーンショット**
   - 実際のアプリ画面をキャプチャ
   - 文字が読みやすく、アプリの機能がわかるもの

## 次のステップ

1. 署名キーの作成
2. リリースAPKのビルド
3. Google Play Console でのアプリ作成
4. 必要な素材（アイコン、スクリーンショット）の準備
5. ストア掲載情報の入力
6. テスト版のリリース
7. 本番環境へのリリース
