# ✅ Laravel Sail + Capacitor 統合開発環境

## 🎉 設定完了！

**ViteからLaravel Sailへの変更が完了しました**

### 📋 新しい設定

| 項目 | 設定値 | 説明 |
|------|--------|------|
| **Laravel Sail** | `localhost:8081` | 開発マシンでのアクセス |
| **Capacitor (エミュレータ)** | `http://10.0.2.2:8081` | エミュレータからのアクセス |
| **phpMyAdmin** | `localhost:8080` | データベース管理 |

### 🚀 統合開発環境の起動

```bash
# 最も簡単な方法（推奨）
npm run dev:laravel

# または
npm run laravel:capacitor:dev

# または手動で
./scripts/start-laravel-capacitor-dev.sh
```

このコマンドは以下を自動実行します：

1. **Laravel Sail起動確認・自動起動**
2. **Laravel Sail (localhost:8081) 動作確認**
3. **エミュレータ起動確認・自動起動**
4. **エミュレータから10.0.2.2:8081への接続テスト**
5. **Capacitor設定の確認・自動設定**
6. **アプリのビルド・同期**
7. **エミュレータへのアプリインストール**
8. **アプリの自動起動**

### 🔧 接続テスト結果

✅ **成功**: エミュレータから10.0.2.2:8081への接続テスト
```bash
# 基本接続テスト
$ adb shell "ping -c 3 10.0.2.2"
✅ 0% packet loss

# HTTP接続テスト  
$ adb shell "echo 'GET / HTTP/1.1...' | nc 10.0.2.2 8081"
✅ HTTP/1.1 200 OK (Laravel response)
```

### 📱 Capacitor設定

**現在の設定** (`capacitor.config.json`):
```json
{
  "server": {
    "url": "http://10.0.2.2:8081",
    "cleartext": true
  }
}
```

### 🛠️ 日常の開発ワークフロー

```bash
# 1. 開発環境起動
npm run dev:laravel

# 2. ログ監視（別ターミナル）
npm run android:log

# 3. 開発終了時
./vendor/bin/sail down              # Laravel Sail停止
./scripts/tastack2-android.sh kill  # エミュレータ終了
```

### ⚡ クイックコマンド

```bash
# Laravel Sail操作
./vendor/bin/sail up -d    # 起動
./vendor/bin/sail down     # 停止
./vendor/bin/sail ps       # 状態確認

# エミュレータ操作
./scripts/tastack2-android.sh e    # エミュレータ起動
./scripts/tastack2-android.sh k    # エミュレータ終了
./scripts/tastack2-android.sh d    # デバイス確認

# アプリ操作
./scripts/tastack2-android.sh st   # アプリ起動
./scripts/tastack2-android.sh l    # ログ監視
```

### 🎯 トラブルシューティング

#### ERR_CONNECTION_REFUSED の場合

1. **Laravel Sailの確認**:
   ```bash
   docker ps | grep laravel
   curl http://localhost:8081
   ```

2. **エミュレータからの接続確認**:
   ```bash
   adb shell "ping -c 1 10.0.2.2"
   adb shell "nc -z 10.0.2.2 8081"
   ```

3. **Capacitor設定の確認**:
   ```bash
   grep -A3 server capacitor.config.json
   ```

### 🌟 この統合の利点

- **No Vite**: Vite開発サーバー不要
- **Laravel直接**: Laravel Sailから直接コンテンツを提供
- **リアルタイム開発**: エミュレータでリアルタイムに開発・デバッグ
- **一括管理**: 一つのコマンドですべて起動
- **自動テスト**: 接続テストが自動実行

これで、ViteなしでLaravel Sail + Capacitorの完全統合開発環境が利用できます！
