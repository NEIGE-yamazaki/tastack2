# iOS デバッグワークフロー

## 🔄 開発・デバッグサイクル

### 1. 環境準備
```bash
# Laravel Sailを起動
npm run sail:up

# Vite開発サーバーを起動
npm run dev
```

### 2. コード変更時の手順
```bash
# フロントエンド変更時
npm run build:mobile
npx cap run ios

# バックエンド変更時（APIなど）
# Sailが起動していれば自動的に反映される
```

### 3. デバッグツールの使い分け

#### Web Inspector (Safari) - 推奨
- **使用場面**: フロントエンド全般のデバッグ
- **接続方法**: Safari → 開発 → Simulator → アプリ
- **確認内容**:
  - JavaScript エラー
  - API通信 (Network タブ)
  - DOM 操作
  - CSS スタイリング

#### Xcode Console
- **使用場面**: ネイティブ機能のデバッグ
- **確認内容**:
  - Capacitorプラグインのエラー
  - ネイティブクラッシュ
  - デバイス権限関連

#### ブラウザ開発者ツール
- **使用場面**: 基本的なWeb開発
- **URL**: http://localhost:5173 (Vite開発サーバー)

## 🚨 よくある問題と解決方法

### 1. 白い画面が表示される
```bash
# ビルドとシンクを再実行
npm run build:mobile
```

### 2. APIが接続できない
- Sailが起動していることを確認: `npm run sail:up`
- ネットワーク設定を確認: Safari Web Inspector → Network

### 3. Capacitorプラグインが動かない
```bash
# iOS依存関係を再インストール
cd ios/App
pod install
cd ../..
npm run build:mobile
```

## 🔧 便利なデバッグコマンド

### ログ確認
```bash
# Sailログ確認
./vendor/bin/sail logs

# iOS Simulatorのリセット
xcrun simctl erase all
```

### ビルド関連
```bash
# 完全リビルド
npm run clean:mobile
npm run build:mobile

# iOS特定ビルド
npx cap run ios --target="iPhone 15"
```

## 📱 デバイス実機デバッグ

### 前提条件
1. Apple Developer Account (無料でも可)
2. デバイスをMacに接続
3. Xcodeでデバイスを信頼済み

### 手順
1. Xcodeでプロジェクトを開く
2. Signing & Capabilities でTeamを選択
3. デバイスを選択してビルド
4. デバイスでアプリを信頼 (設定 → 一般 → デバイス管理)

### 実機でのWeb Inspector接続
1. デバイスでSafariを開く
2. 設定 → Safari → 詳細 → Web Inspector をON
3. MacのSafari → 開発 → デバイス名 → アプリ
