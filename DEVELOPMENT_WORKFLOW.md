# 開発ワークフロー

## 🚀 開発環境構成

### プロジェクト構成
```
tastack2/
├── Laravel Backend (API)
├── Vite Frontend (Web)
├── Capacitor (Mobile)
├── Android Studio (Android)
└── Docker Sail (開発環境)
```

### 🔧 便利なコマンド

#### システム全体で使用可能
```bash
# どのディレクトリからでも使用可能
tastack2 studio      # Android Studio起動
tastack2 log         # ログ監視
tastack2 build       # APKビルド
tastack2 devices     # デバイス確認
tastack2 help        # ヘルプ表示
```

#### プロジェクトディレクトリ内
```bash
# プロジェクトディレクトリに移動
tastack2-cd

# Android Studio起動
tastack2-studio
```

## 📋 日常的な開発フロー

### 1. 開発サーバー起動
```bash
# Laravel Sail起動（バックエンド）
npm run sail:up

# Vite開発サーバー起動（フロントエンド）
npm run dev
```

### 2. モバイル開発
```bash
# Webアセットビルド + Capacitor同期
npm run build:mobile

# Android開発
npm run android:studio        # Android Studio起動
npm run android:build         # APKビルド
npm run android:run           # エミュレータ起動

# iOS開発
npm run cap:open:ios          # Xcode起動
```

### 3. デバッグ・テスト
```bash
# Android
npm run android:logcat        # ログ確認
npm run android:devices       # デバイス確認
npm run android:emulator:list # エミュレータ一覧

# Laravel
npm run sail:artisan          # Artisanコマンド
npm run sail:test             # テスト実行
```

## 🔄 効率的な開発パターン

### パターン1: Web開発メイン
```bash
# 1. Sail起動
npm run sail:up

# 2. 開発サーバー起動
npm run dev

# 3. ブラウザで開発
open http://localhost:5173
```

### パターン2: Android開発メイン
```bash
# 1. Webビルド
npm run build:mobile

# 2. Android Studio起動
npm run android:studio

# 3. エミュレータでテスト
npm run android:run
```

### パターン3: フルスタック開発
```bash
# 1. 全サービス起動
npm run sail:up && npm run dev

# 2. 並行開発
# Terminal 1: Web開発
# Terminal 2: Mobile開発
# Terminal 3: API開発
```

## 🛠️ VSCode推奨設定

### 推奨拡張機能
- PHP Intelephense
- Laravel Blade Snippets
- Vite
- Alpine.js IntelliSense
- Android Studio Tools

### tasks.json利用
```bash
# VSCode内でタスク実行
Ctrl+Shift+P → "Tasks: Run Task"

# 利用可能なタスク
- Laravel Sail Up/Down
- Vite Dev Server
- Build for Mobile
- Capacitor Sync
- Android Studio起動
```

## 📱 モバイル開発Tips

### Android開発
```bash
# 高速ビルド
npm run android:clean && npm run android:build

# エミュレータ管理
npm run android:emulator:list  # 一覧
npm run android:emulator       # 起動
npm run android:emulator:kill  # 終了
```

### iOS開発
```bash
# プロジェクト同期
npm run build:mobile

# Xcode起動
npm run cap:open:ios
```

## 🔧 トラブルシューティング

### よくある問題と解決法

1. **Capacitor同期エラー**
   ```bash
   npm run clean:mobile
   npm run build:mobile
   ```

2. **Android Studio起動エラー**
   ```bash
   # 環境変数確認
   echo $ANDROID_HOME
   echo $JAVA_HOME
   
   # 環境リセット
   ./scripts/quick-android-setup.sh
   ```

3. **Gradle ビルドエラー**
   ```bash
   cd android
   ./gradlew clean
   ./gradlew assembleDebug
   ```

## 🎯 開発効率化のコツ

### 1. 並行開発
- **Terminal 1**: `npm run dev` (Web開発)
- **Terminal 2**: `npm run sail:up` (API開発)
- **Terminal 3**: モバイル開発用

### 2. ホットリロード活用
- Vite: Web画面の即座反映
- Capacitor: ライブリロード対応

### 3. デバッグ最適化
- Chrome DevTools: Web版デバッグ
- Android Studio: Android版デバッグ  
- Xcode: iOS版デバッグ

## 📊 パフォーマンス最適化

### ビルド時間短縮
```bash
# 差分ビルド
npm run build:mobile

# クリーンビルド（必要時のみ）
npm run clean && npm run build:mobile
```

### メモリ使用量最適化
```bash
# Docker リソース制限
# Docker Desktop設定で調整

# Gradle メモリ設定
# android/gradle.properties
# org.gradle.jvmargs=-Xmx2048m
```

## 🌟 推奨開発環境

### 最適なワークフロー
1. **朝の開発開始**
   ```bash
   npm run sail:up && npm run dev
   ```

2. **モバイルテスト時**
   ```bash
   npm run build:mobile
   npm run android:studio
   ```

3. **開発終了時**
   ```bash
   npm run sail:down
   ```

### 品質保証
```bash
# プリコミット
npm run sync:windows  # Windows環境確認
npm run sync:mac      # Mac環境確認

# テスト実行
npm run sail:test
```

このワークフローに従うことで、効率的かつ品質の高い開発が可能になります。
