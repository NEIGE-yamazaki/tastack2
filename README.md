# tastack2

**Laravel + Capacitor.js + Alpine.js** によるクロスプラットフォーム開発プロジェクト

## 🌟 特徴

- **Web**: Laravel 11 + Vite + Alpine.js
- **Mobile**: Capacitor.js (iOS/Android対応)
- **日本語対応**: タイムゾーン(Asia/Tokyo)、ロケール(ja)設定済み
- **開発環境**: Docker Sail + phpMyAdmin
- **クロスプラットフォーム**: Windows ↔ Mac 開発同期

## 🚀 クイックスタート

### macOS環境での初期セットアップ ✅ **完了済み**
```bash
# 自動セットアップスクリプト実行
./setup-macos.sh

# プロジェクトセットアップ
npm run setup:mac

# 開発サーバー起動
npm run dev

# iOS開発開始
npm run cap:open:ios
```

**セットアップ状況**: 📋 [SETUP_STATUS.md](./SETUP_STATUS.md) を参照

### Windows環境での初期セットアップ
```bash
# プロジェクトクローン
git clone https://github.com/NEIGE-yamazaki/tanstack2.git
cd tanstack2

# Windows環境セットアップ
npm run setup:windows

# Android開発環境セットアップ（Android Studio必須）
bash scripts/setup-android-windows.sh

# Docker環境起動
./vendor/bin/sail up -d

# Webアプリケーション起動
npm run dev

# Android開発開始
npm run cap:open:android
```

## 🔄 クロスプラットフォーム開発ワークフロー

### Windows → Mac 同期
```bash
# Windows側: 変更をプッシュ
npm run sync:windows  # チェック実行
git add .
git commit -m "新機能追加"
git push origin main

# Mac側: 変更を同期してiOS開発
npm run sync:mac  # 自動同期＆iOS環境準備
```

詳細は [Windows↔Mac同期ガイド](./windows-mac-sync-guide.md) を参照

## 📱 モバイル開発

### Android (Windows推奨)
```bash
# 初回セットアップ（Android Studio必須）
bash scripts/setup-android-windows.sh

# Android Studio で開発
npm run cap:open:android

# APKビルド（簡単）
npm run build:android  # デバッグ版
npm run build:android:release  # リリース版

# 手動ビルド
npm run build:mobile
cd android && ./gradlew assembleDebug
```

### iOS (Mac限定)
```bash
# Xcode で開発
npm run cap:open:ios

# iOS Simulator での実行
npm run cap:run:ios
```

## 🛠️ 利用可能なスクリプト

| コマンド | 説明 |
|----------|------|
| `npm run dev` | Vite開発サーバー起動 |
| `npm run build:mobile` | モバイル向けビルド＋同期 |
| `npm run sync:windows` | Windows用プッシュ前チェック |
| `npm run sync:mac` | Mac用同期＆iOS環境準備 |
| `npm run env:windows` | Windows用環境設定 |
| `npm run env:mac` | Mac用環境設定 |
| `npm run clean` | 依存関係クリーンインストール |
| `npm run clean:mobile` | Capacitorクリーン同期 |

## 🔧 設定ファイル

- `.env.windows.example` - Windows環境用設定テンプレート
- `.env.mac.example` - Mac環境用設定テンプレート  
- `capacitor.config.json` - Capacitor設定
- `.vscode/` - VS Code ワークスペース設定

## 📖 詳細ドキュメント

- [Windows↔Mac同期ガイド](./windows-mac-sync-guide.md)
- [Mac環境セットアップ](./setup-macos.sh)
- [モバイル機能テスト](http://localhost:8081/mobile-test)

---

<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[WebReinvent](https://webreinvent.com/)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Jump24](https://jump24.co.uk)**
- **[Redberry](https://redberry.international/laravel/)**
- **[Active Logic](https://activelogic.com)**
- **[byte5](https://byte5.de)**
- **[OP.GG](https://op.gg)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
