#!/bin/bash

# VSCode Git トラブルシューティング 便利コマンド集

# エイリアス定義
alias vscode-git-help='cat /home/user/tastack2/docs/vscode-git-troubleshooting.md'
alias vscode-git-fix='/home/user/tastack2/scripts/fix-vscode-git.sh'
alias vscode-git-status='git status && git log --oneline -5 && git remote -v'
alias vscode-git-config='git config --list | grep -E "(user|core|remote)"'

# 使用方法の表示
echo "🔧 VSCode Git トラブルシューティング コマンド"
echo ""
echo "利用可能なコマンド:"
echo "  vscode-git-help    - トラブルシューティングガイドを表示"
echo "  vscode-git-fix     - 自動診断・修復スクリプトを実行"
echo "  vscode-git-status  - Git リポジトリの状態を確認"
echo "  vscode-git-config  - Git 設定を確認"
echo ""
echo "使用例:"
echo "  source scripts/vscode-git-aliases.sh"
echo "  vscode-git-help"
echo ""

# 自動で.bashrcに追加するかの確認
if [ -f ~/.bashrc ]; then
    echo "💡 これらのエイリアスを永続化したい場合:"
    echo "   echo 'source /home/user/tastack2/scripts/vscode-git-aliases.sh' >> ~/.bashrc"
    echo "   source ~/.bashrc"
fi
