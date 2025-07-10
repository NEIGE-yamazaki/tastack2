#!/bin/bash

# VSCode Git 問題解決スクリプト

echo "🔧 VSCodeソース管理の問題解決を開始します..."

# 1. Git設定の確認
echo "📋 Git設定の確認..."
git config --list | grep -E "(user\.name|user\.email|core\.editor)"

# 2. VSCodeの設定確認
echo "📋 VSCodeワークスペース設定の確認..."
if [ -f ".vscode/settings.json" ]; then
    echo "✅ .vscode/settings.json が存在します"
else
    echo "⚠️  .vscode/settings.json が存在しません - 作成します"
    mkdir -p .vscode
    cat > .vscode/settings.json << 'EOF'
{
    "git.enabled": true,
    "git.autoRepositoryDetection": "openEditors",
    "git.path": "git",
    "git.detectSubmodules": true,
    "git.autofetch": true,
    "git.enableSmartCommit": true,
    "git.confirmSync": false,
    "scm.defaultViewMode": "tree",
    "scm.alwaysShowProviders": true,
    "scm.repositories.visible": 10
}
EOF
fi

# 3. Git index の修復
echo "🔧 Git index の修復..."
git status --porcelain

# 4. VSCodeの再起動を促す
echo "🔄 VSCodeの再起動が必要な場合があります"
echo "   - Ctrl+Shift+P → 'Developer: Reload Window'"
echo "   - または VSCode を完全に再起動"

# 5. Git パーミッションの確認
echo "📋 Git パーミッションの確認..."
ls -la .git/

# 6. 現在のブランチとリモートの確認
echo "📋 現在のブランチとリモートの確認..."
git branch -a
git remote -v

echo "✅ 診断完了"

# 解決策の提示
echo ""
echo "🚨 VSCodeソース管理が動作しない場合の解決策："
echo "1. VSCodeを再起動 (Ctrl+Shift+P → 'Developer: Reload Window')"
echo "2. フォルダーを再度開く (File → Open Folder)"
echo "3. Git拡張機能を再有効化"
echo "4. ソース管理パネルを更新 (Ctrl+Shift+G)"
echo "5. VSCodeの設定を確認 (settings.json)"
echo ""
echo "📖 問題が続く場合は、以下をお試しください："
echo "- VSCodeの拡張機能「Git」を無効化→有効化"
echo "- VSCodeの設定で git.enabled が true になっているか確認"
echo "- コマンドパレットで 'Git: Refresh' を実行"
