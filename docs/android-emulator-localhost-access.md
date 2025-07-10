# TanStack2 Android開発環境 - エミュレータアクセス対応

## 概要

このドキュメントは、TanStack2 Androidアプリの開発環境において、エミュレータから開発マシンのローカルサーバーにアクセスするためのセットアップと使用方法を説明します。

## 重要な設定変更

### エミュレータのネットワーク設定

Androidエミュレータは特別なネットワーク環境で動作するため、以下の設定が必要です：

- **localhost → 10.0.2.2**: エミュレータから開発マシンにアクセスする際の特別なIPアドレス
- **ポート**: Vite開発サーバーのポート（5173）を使用

### 設定ファイルの分離

プロジェクトでは以下の設定ファイルを使い分けています：

```
capacitor.config.json           # 開発環境用（エミュレータ対応）
capacitor.config.emulator.json  # エミュレータ専用設定
capacitor.config.production.json # 本番環境用設定
```

## 自動化された開発ワークフロー

### 1. ワンクリック開発環境起動

```bash
# すべてを一括で起動
npm run android:full:dev

# または個別に実行
npm run android:dev
```

このコマンドは以下を自動実行します：
1. エミュレータの起動確認・自動起動
2. Laravel Sailの起動確認・自動起動
3. Vite開発サーバーの起動確認・自動起動
4. アプリのビルド・同期
5. エミュレータへのアプリインストール
6. アプリの自動起動

### 2. 設定の切り替え

```bash
# エミュレータ用設定に切り替え
npm run android:config:emulator

# 本番用設定に切り替え
npm run android:config:production

# 開発用設定に戻す
npm run android:config:dev
```

### 3. 個別操作

```bash
# エミュレータ操作
npm run android:emulator     # エミュレータ起動
npm run android:devices      # 接続デバイス確認
npm run android:emulator:kill # エミュレータ終了

# アプリ操作
npm run android:build        # アプリビルド
npm run android:install      # アプリインストール
npm run android:run          # アプリ起動

# ログ監視
npm run android:log          # 詳細ログ監視
npm run android:log:quick    # クイックログ確認
```

## 技術的詳細

### ネットワーク設定

**Vite設定** (`vite.config.js`):
```javascript
server: {
    host: '0.0.0.0',  // すべてのネットワークインターフェースでリッスン
    port: 5173,       // ポート番号
    strictPort: true
}
```

**Capacitor設定** (`capacitor.config.json`):
```json
{
    "server": {
        "url": "http://10.0.2.2:5173",  // エミュレータから開発マシンへのアクセス
        "cleartext": true               // HTTP通信を許可
    }
}
```

### エミュレータネットワーク仕様

| アドレス | 説明 |
|---------|------|
| `10.0.2.2` | 開発マシンのlocalhostに相当 |
| `10.0.2.3` | 最初のDNSサーバー |
| `10.0.2.1` | ゲートウェイ |

## VSCodeタスク

VS Codeのコマンドパレット（Ctrl+Shift+P）から以下のタスクを実行できます：

- **Android: 開発環境一括起動** - 全自動でエミュレータ開発環境を起動
- **Android: フル開発環境（設定切り替え＋起動）** - 設定切り替え＋起動を一括実行
- **Android: 設定をエミュレータ用に切り替え** - エミュレータ用設定に切り替え
- **Android: 設定を本番用に切り替え** - 本番用設定に切り替え

## トラブルシューティング

### エミュレータからアクセスできない場合

1. **Vite開発サーバーの確認**:
   ```bash
   # サーバーが0.0.0.0:5173でリッスンしているか確認
   netstat -an | grep 5173
   ```

2. **Capacitor設定の確認**:
   ```bash
   # 現在の設定を確認
   cat capacitor.config.json | grep -A3 server
   ```

3. **エミュレータの再起動**:
   ```bash
   npm run android:emulator:kill
   npm run android:emulator
   ```

### パフォーマンスの問題

1. **エミュレータのスペック確認**:
   ```bash
   # エミュレータの設定を確認
   npm run android:emulator:list
   ```

2. **アプリの再インストール**:
   ```bash
   npm run android:clean
   npm run android:build
   npm run android:install
   ```

## 開発フロー例

```bash
# 1. 開発環境を一括起動
npm run android:full:dev

# 2. コードを編集（自動リロード）

# 3. 必要に応じてログを監視
npm run android:log:quick

# 4. 本番ビルドの場合
npm run android:full:build
```

このワークフローにより、エミュレータでの開発が効率化され、リアルタイムでの開発・デバッグが可能になります。
