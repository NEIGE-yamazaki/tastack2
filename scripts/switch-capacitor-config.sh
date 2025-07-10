#!/bin/bash

# Capacitor設定ファイル切り替えスクリプト
# 使用方法: ./scripts/switch-capacitor-config.sh [development|emulator|production]

set -e

CONFIG_TYPE="${1:-development}"

# 色定義
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

show_status() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

show_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

show_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# 使用方法を表示
show_usage() {
    echo "使用方法: $0 [development|emulator|production]"
    echo ""
    echo "設定タイプ:"
    echo "  development - 開発環境（デフォルト）"
    echo "  emulator    - エミュレータ環境"
    echo "  production  - 本番環境"
    echo ""
    echo "例:"
    echo "  $0 emulator    # エミュレータ用設定に切り替え"
    echo "  $0 production  # 本番用設定に切り替え"
}

# 引数チェック
if [[ "$CONFIG_TYPE" != "development" && "$CONFIG_TYPE" != "emulator" && "$CONFIG_TYPE" != "production" ]]; then
    show_error "無効な設定タイプです: $CONFIG_TYPE"
    show_usage
    exit 1
fi

# 設定ファイルの存在確認
case "$CONFIG_TYPE" in
    "development")
        CONFIG_FILE="capacitor.config.json"
        DESCRIPTION="開発環境用設定"
        ;;
    "emulator")
        CONFIG_FILE="capacitor.config.emulator.json"
        DESCRIPTION="エミュレータ用設定"
        ;;
    "production")
        CONFIG_FILE="capacitor.config.production.json"
        DESCRIPTION="本番環境用設定"
        ;;
esac

if [ ! -f "$CONFIG_FILE" ]; then
    show_error "設定ファイルが見つかりません: $CONFIG_FILE"
    exit 1
fi

# 現在の設定をバックアップ
if [ -f "capacitor.config.json" ] && [ "$CONFIG_TYPE" != "development" ]; then
    cp capacitor.config.json capacitor.config.json.backup
    show_status "現在の設定をバックアップしました"
fi

# 設定を切り替え
if [ "$CONFIG_TYPE" == "development" ]; then
    show_status "開発環境用設定を使用します"
else
    cp "$CONFIG_FILE" capacitor.config.json
    show_success "$DESCRIPTION に切り替えました"
fi

# 設定内容を表示
show_status "現在の設定:"
if grep -q '"server"' capacitor.config.json; then
    SERVER_URL=$(grep -A2 '"server"' capacitor.config.json | grep '"url"' | cut -d'"' -f4)
    echo "  - サーバーURL: $SERVER_URL"
else
    echo "  - サーバーURL: なし（本番環境）"
fi

ALLOW_MIXED=$(grep -A3 '"android"' capacitor.config.json | grep '"allowMixedContent"' | cut -d':' -f2 | tr -d ' ,')
echo "  - Mixed Content: $ALLOW_MIXED"

echo ""
show_success "設定の切り替えが完了しました"
echo ""
echo "次のステップ:"
echo "  1. npm run cap:sync       # 設定を同期"
echo "  2. npm run android:build  # アプリをビルド"
echo "  3. npm run android:install # アプリをインストール"
