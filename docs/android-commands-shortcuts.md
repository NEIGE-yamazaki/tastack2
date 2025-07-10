# ✅ TASTACK2 Android開発コマンド - 番号・英文字対応完了

## 🎉 新機能: 番号・英文字でのコマンド実行

すべてのコマンドが以下の3つの方法で実行できるようになりました：

### 📋 コマンド対応表

| 番号 | 英文字 | フルネーム | 説明 |
|------|--------|------------|------|
| `1` | `s` | `studio` | Android Studio起動 |
| `2` | `ss` | `studio-safe` | Android Studio安全起動 |
| `3` | `l` | `log` | ログ監視開始 |
| `4` | `lq` | `log-quick` | クイックログ確認 |
| `5` | `b` | `build` | APKビルド |
| `6` | `i` | `install` | アプリインストール |
| `7` | `d` | `devices` | デバイス確認 |
| `8` | `e` | `emulator` | エミュレータ起動 |
| `9` | `k` | `kill` | エミュレータ終了 |
| `10` | `f` | `full-run` | 完全開発フロー |
| - | `dev` | `dev` | full-runの短縮版 |
| `11` | `r` | `run` | エミュレータ起動 + ビルド + インストール |
| `12` | `st` | `start` | アプリ起動 |
| `13` | `in` | `info` | 環境情報表示 |
| `0` | `h` | `help` | ヘルプ表示 |

### 🚀 使用例

```bash
# すべて同じ動作（デバイス確認）
./scripts/tastack2-android.sh 7
./scripts/tastack2-android.sh d
./scripts/tastack2-android.sh devices

# すべて同じ動作（完全開発フロー）
./scripts/tastack2-android.sh 10
./scripts/tastack2-android.sh f
./scripts/tastack2-android.sh full-run
./scripts/tastack2-android.sh dev

# すべて同じ動作（ヘルプ表示）
./scripts/tastack2-android.sh 0
./scripts/tastack2-android.sh h
./scripts/tastack2-android.sh help
./scripts/tastack2-android.sh    # 引数なし
```

### ⚡ 最もよく使われるコマンド（推奨）

```bash
# 開発開始 - 最も重要！
./scripts/tastack2-android.sh dev     # または f または 10

# 素早い操作
./scripts/tastack2-android.sh k       # エミュレータ終了
./scripts/tastack2-android.sh d       # デバイス確認
./scripts/tastack2-android.sh l       # ログ監視

# ヘルプ確認
./scripts/tastack2-android.sh h       # または 0
```

### 🔥 開発効率化のポイント

1. **番号で覚えやすく**: `./scripts/tastack2-android.sh 10` でフル開発フロー
2. **英文字で直感的**: `./scripts/tastack2-android.sh dev` でフル開発フロー
3. **短縮形でスピーディ**: `./scripts/tastack2-android.sh k` でエミュレータ終了

### 🎯 ワークフロー例

```bash
# 1. 開発開始
./scripts/tastack2-android.sh dev

# 2. ログ監視（別ターミナル）
./scripts/tastack2-android.sh l

# 3. 開発終了
./scripts/tastack2-android.sh k
```

この改善により、Android開発のコマンド入力が大幅に効率化されました！
