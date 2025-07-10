# TASTACK2 Android開発コマンド - 最新版

## 新しい便利なコマンド

### 🚀 完全開発フロー（推奨）

```bash
# エミュレータ起動 + ビルド + インストール + アプリ起動を一括実行
./scripts/tastack2-android.sh full-run
# または短縮版
./scripts/tastack2-android.sh dev

# npm scriptでも実行可能
npm run android:dev:complete
npm run android:dev:short
```

### ⏹️ エミュレータ終了

```bash
# エミュレータを安全に終了
./scripts/tastack2-android.sh kill

# npm scriptでも実行可能
npm run android:emulator:kill
```

## 全コマンド一覧

| コマンド | 説明 | 実行内容 |
|---------|------|----------|
| `studio` | Android Studio起動 | 環境変数設定 + Android Studio起動 |
| `studio-safe` | Android Studio安全起動 | 専用スクリプトでAndroid Studio起動 |
| `log` | ログ監視開始 | インタラクティブログ監視 |
| `log-quick` | クイックログ確認 | 簡単なログ確認 |
| `build` | APKビルド | デバイス確認 + APKビルド |
| `install` | アプリインストール | デバイス確認 + インストール |
| `devices` | デバイス確認 | 接続デバイス一覧表示 |
| `emulator` | エミュレータ起動 | エミュレータ起動 |
| `kill` | エミュレータ終了 | エミュレータ安全終了 |
| `run` | 基本開発フロー | エミュレータ起動 + ビルド + インストール |
| `full-run` | 完全開発フロー | エミュレータ起動 + ビルド + インストール + アプリ起動 |
| `dev` | full-runの短縮版 | 完全開発フローの短縮コマンド |
| `start` | アプリ起動 | アプリ起動 + プロセス確認 |
| `info` | 環境情報表示 | 環境変数・デバイス・パス確認 |
| `help` | ヘルプ表示 | 使用方法とコマンド一覧 |

## 推奨ワークフロー

### 1. 初回開発環境セットアップ
```bash
# Capacitor設定をエミュレータ用に切り替え
npm run android:config:emulator

# 完全開発フローを実行
./scripts/tastack2-android.sh full-run
```

### 2. 日常の開発作業
```bash
# 開発環境起動（最も使用頻度が高い）
./scripts/tastack2-android.sh dev

# ログ監視（別ターミナル）
./scripts/tastack2-android.sh log

# 作業終了時
./scripts/tastack2-android.sh kill
```

### 3. デバッグ・トラブルシューティング
```bash
# デバイス確認
./scripts/tastack2-android.sh devices

# 環境情報確認
./scripts/tastack2-android.sh info

# Android Studio起動
./scripts/tastack2-android.sh studio
```

## VSCodeタスク統合

VS Codeのコマンドパレット（Ctrl+Shift+P）から以下のタスクを実行できます：

- **Android: 完全開発フロー（起動+ビルド+インストール+アプリ起動）**
- **Android: エミュレータ終了**
- **Android: 開発環境一括起動**
- **Android: 設定をエミュレータ用に切り替え**

## エラー対応

### エミュレータが起動しない場合
```bash
# エミュレータ一覧確認
./scripts/tastack2-android.sh devices

# 環境情報確認
./scripts/tastack2-android.sh info

# 強制終了してからやり直し
./scripts/tastack2-android.sh kill
./scripts/tastack2-android.sh full-run
```

### アプリがインストールできない場合
```bash
# デバイス確認
./scripts/tastack2-android.sh devices

# ビルドからやり直し
./scripts/tastack2-android.sh build
./scripts/tastack2-android.sh install
```

これらのコマンドにより、Android開発のワークフローが大幅に効率化されます。
