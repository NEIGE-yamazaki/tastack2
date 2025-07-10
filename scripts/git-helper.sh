#!/bin/bash

# Git管理 便利シェルスクリプト

# 色定義
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
PURPLE='\033[0;35m'
CYAN='\033[0;36m'
NC='\033[0m' # No Color

# 関数定義

# Git状態の表示
git_status() {
    echo -e "${BLUE}📊 Git Status${NC}"
    echo "==============================="
    git status --short
    echo ""
    echo -e "${PURPLE}🔍 Recent Commits:${NC}"
    git log --oneline -5
    echo ""
    echo -e "${CYAN}🌐 Remote Info:${NC}"
    git remote -v
    echo ""
}

# Git統計情報の表示
git_stats() {
    echo -e "${GREEN}📈 Git Statistics${NC}"
    echo "==============================="
    echo "Total commits: $(git rev-list --all --count)"
    echo "Branches: $(git branch -a | wc -l)"
    echo "Contributors: $(git shortlog -sn | wc -l)"
    echo "Repository size: $(du -sh .git | cut -f1)"
    echo ""
    echo -e "${YELLOW}Top Contributors:${NC}"
    git shortlog -sn | head -5
    echo ""
}

# ブランチ一覧の表示
git_branches() {
    echo -e "${BLUE}🌿 Branch Information${NC}"
    echo "==============================="
    echo -e "${GREEN}Local Branches:${NC}"
    git branch -v
    echo ""
    echo -e "${CYAN}Remote Branches:${NC}"
    git branch -r
    echo ""
    echo -e "${YELLOW}Current Branch:${NC}"
    git branch --show-current
    echo ""
}

# 変更されたファイルの詳細表示
git_changes() {
    echo -e "${PURPLE}📝 Changes Details${NC}"
    echo "==============================="
    echo -e "${YELLOW}Modified Files:${NC}"
    git diff --name-only
    echo ""
    echo -e "${CYAN}Staged Files:${NC}"
    git diff --cached --name-only
    echo ""
    echo -e "${GREEN}Untracked Files:${NC}"
    git ls-files --others --exclude-standard
    echo ""
}

# 便利なGitコマンド
git_quick_commit() {
    local message="$1"
    if [ -z "$message" ]; then
        echo -e "${RED}❌ Error: Commit message required${NC}"
        echo "Usage: git_quick_commit \"Your commit message\""
        return 1
    fi
    
    echo -e "${YELLOW}🚀 Quick Commit${NC}"
    echo "==============================="
    git add -A
    git commit -m "$message"
    echo -e "${GREEN}✅ Committed successfully!${NC}"
}

# 安全なプッシュ
git_safe_push() {
    echo -e "${BLUE}🔒 Safe Push${NC}"
    echo "==============================="
    
    # 現在のブランチを取得
    current_branch=$(git branch --show-current)
    
    # リモートの最新情報を取得
    echo "Fetching latest changes..."
    git fetch origin
    
    # ローカルとリモートの差分を確認
    local_commits=$(git rev-list --count HEAD ^origin/$current_branch)
    remote_commits=$(git rev-list --count origin/$current_branch ^HEAD)
    
    if [ "$remote_commits" -gt 0 ]; then
        echo -e "${YELLOW}⚠️  Remote has $remote_commits new commits${NC}"
        echo "Consider pulling first: git pull origin $current_branch"
        read -p "Continue push? (y/N): " -n 1 -r
        echo
        if [[ ! $REPLY =~ ^[Yy]$ ]]; then
            echo "Push cancelled."
            return 1
        fi
    fi
    
    echo "Pushing to origin/$current_branch..."
    git push origin $current_branch
    echo -e "${GREEN}✅ Push completed!${NC}"
}

# Gitログの美しい表示
git_log_pretty() {
    echo -e "${PURPLE}📜 Pretty Git Log${NC}"
    echo "==============================="
    git log --graph --pretty=format:'%Cred%h%Creset -%C(yellow)%d%Creset %s %Cgreen(%cr) %C(bold blue)<%an>%Creset' --abbrev-commit --date=relative -10
    echo ""
}

# 未追跡ファイルの表示
git_untracked() {
    echo -e "${YELLOW}📂 Untracked Files${NC}"
    echo "==============================="
    git ls-files --others --exclude-standard
    echo ""
}

# Gitクリーンアップ
git_cleanup() {
    echo -e "${CYAN}🧹 Git Cleanup${NC}"
    echo "==============================="
    echo "Cleaning up Git repository..."
    
    # ガベージコレクション
    git gc --prune=now
    
    # 不要なブランチの表示
    echo -e "${YELLOW}Merged branches (can be deleted):${NC}"
    git branch --merged | grep -v "\*\|master\|main\|develop"
    
    echo -e "${GREEN}✅ Cleanup completed!${NC}"
}

# ヘルプ表示
show_help() {
    echo -e "${BLUE}🔧 Git Helper Script${NC}"
    echo "==============================="
    echo "Available commands:"
    echo "  git_status          - Show git status and recent commits"
    echo "  git_stats           - Show repository statistics"
    echo "  git_branches        - Show branch information"
    echo "  git_changes         - Show detailed changes"
    echo "  git_quick_commit    - Quick add and commit"
    echo "  git_safe_push       - Safe push with checks"
    echo "  git_log_pretty      - Pretty git log display"
    echo "  git_untracked       - Show untracked files"
    echo "  git_cleanup         - Clean up repository"
    echo "  show_help           - Show this help"
    echo ""
    echo "Usage examples:"
    echo "  git_quick_commit \"Fix bug in login function\""
    echo "  git_safe_push"
    echo "  git_status"
    echo ""
}

# メイン処理
main() {
    if [ $# -eq 0 ]; then
        show_help
        return 0
    fi
    
    case "$1" in
        "status")
            git_status
            ;;
        "stats")
            git_stats
            ;;
        "branches")
            git_branches
            ;;
        "changes")
            git_changes
            ;;
        "commit")
            git_quick_commit "$2"
            ;;
        "push")
            git_safe_push
            ;;
        "log")
            git_log_pretty
            ;;
        "untracked")
            git_untracked
            ;;
        "cleanup")
            git_cleanup
            ;;
        "help"|"-h"|"--help")
            show_help
            ;;
        *)
            echo -e "${RED}❌ Unknown command: $1${NC}"
            show_help
            ;;
    esac
}

# スクリプトが直接実行された場合
if [[ "${BASH_SOURCE[0]}" == "${0}" ]]; then
    main "$@"
fi
