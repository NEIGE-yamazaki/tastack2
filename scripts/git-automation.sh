#!/bin/bash

# Git自動化スクリプト - 日常的なGitタスクを自動化

# 設定
AUTO_COMMIT_INTERVAL=60  # 分
BACKUP_INTERVAL=240      # 分

# 色定義
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

# 自動コミット（WIP）
auto_commit_wip() {
    echo -e "${BLUE}🔄 Auto-committing WIP...${NC}"
    
    if ! git diff-index --quiet HEAD --; then
        local timestamp=$(date '+%Y-%m-%d %H:%M:%S')
        git add -A
        git commit -m "WIP: Auto-commit at $timestamp"
        echo -e "${GREEN}✅ WIP committed${NC}"
    else
        echo "No changes to commit."
    fi
}

# 自動プッシュ（安全チェック付き）
auto_push_safe() {
    echo -e "${BLUE}🚀 Auto-pushing safely...${NC}"
    
    local current_branch=$(git branch --show-current)
    
    # WIPコミットがある場合は警告
    if git log --oneline -1 | grep -q "WIP:"; then
        echo -e "${YELLOW}⚠️  WIP commit detected. Skipping auto-push.${NC}"
        return 1
    fi
    
    # リモートと同期チェック
    git fetch origin
    local behind_count=$(git rev-list --count HEAD..origin/$current_branch)
    
    if [ "$behind_count" -gt 0 ]; then
        echo -e "${YELLOW}⚠️  Branch is behind remote. Pulling first...${NC}"
        git pull origin $current_branch
    fi
    
    # プッシュ実行
    git push origin $current_branch
    echo -e "${GREEN}✅ Auto-push completed${NC}"
}

# 定期的なバックアップ
periodic_backup() {
    echo -e "${BLUE}💾 Creating periodic backup...${NC}"
    
    local backup_branch="auto-backup-$(date +%Y%m%d-%H%M)"
    local current_branch=$(git branch --show-current)
    
    # 変更があるかチェック
    if ! git diff-index --quiet HEAD --; then
        git stash push -m "Auto-backup stash"
        git checkout -b $backup_branch
        git stash pop
        git add -A
        git commit -m "Automatic backup: $(date '+%Y-%m-%d %H:%M:%S')"
        git checkout $current_branch
        echo -e "${GREEN}✅ Backup created: $backup_branch${NC}"
    else
        echo "No changes to backup."
    fi
}

# プロジェクトの健全性チェック
health_check() {
    echo -e "${BLUE}🔍 Running project health check...${NC}"
    
    local issues=0
    
    # 大きなファイルのチェック
    echo "Checking for large files..."
    local large_files=$(find . -name .git -prune -o -type f -size +50M -print)
    if [ -n "$large_files" ]; then
        echo -e "${YELLOW}⚠️  Large files found:${NC}"
        echo "$large_files"
        ((issues++))
    fi
    
    # 機密情報のチェック
    echo "Checking for potential secrets..."
    local secrets=$(git grep -i -E "(password|secret|api.?key|token)" -- '*.js' '*.php' '*.json' '*.env*' 2>/dev/null | head -5)
    if [ -n "$secrets" ]; then
        echo -e "${RED}🚨 Potential secrets found:${NC}"
        echo "$secrets"
        ((issues++))
    fi
    
    # 未追跡ファイルのチェック
    echo "Checking for untracked files..."
    local untracked=$(git ls-files --others --exclude-standard | wc -l)
    if [ "$untracked" -gt 10 ]; then
        echo -e "${YELLOW}⚠️  Many untracked files: $untracked${NC}"
        ((issues++))
    fi
    
    # 結果表示
    if [ "$issues" -eq 0 ]; then
        echo -e "${GREEN}✅ Project health check passed${NC}"
    else
        echo -e "${RED}❌ Health check found $issues issues${NC}"
    fi
    
    return $issues
}

# 統計レポートの生成
generate_report() {
    echo -e "${BLUE}📊 Generating Git report...${NC}"
    
    local report_file="git-report-$(date +%Y%m%d).md"
    
    cat > $report_file << EOF
# Git Report - $(date '+%Y-%m-%d')

## Repository Statistics
- Total commits: $(git rev-list --all --count)
- Branches: $(git branch -a | wc -l)
- Contributors: $(git shortlog -sn | wc -l)
- Repository size: $(du -sh .git | cut -f1)

## Recent Activity
### Last 10 commits
\`\`\`
$(git log --oneline -10)
\`\`\`

### Contributors (last 30 days)
\`\`\`
$(git shortlog -sn --since="30 days ago")
\`\`\`

## Branch Status
### Local branches
\`\`\`
$(git branch -v)
\`\`\`

### Remote branches
\`\`\`
$(git branch -r)
\`\`\`

## Files Status
### Modified files
\`\`\`
$(git diff --name-only)
\`\`\`

### Staged files
\`\`\`
$(git diff --cached --name-only)
\`\`\`

### Untracked files
\`\`\`
$(git ls-files --others --exclude-standard)
\`\`\`

---
Generated at: $(date '+%Y-%m-%d %H:%M:%S')
EOF
    
    echo -e "${GREEN}✅ Report generated: $report_file${NC}"
}

# 監視モード
watch_mode() {
    echo -e "${BLUE}👁️  Starting watch mode...${NC}"
    echo "Press Ctrl+C to stop"
    
    while true; do
        echo -e "${CYAN}[$(date '+%H:%M:%S')] Checking...${NC}"
        
        # 変更があるかチェック
        if ! git diff-index --quiet HEAD --; then
            echo "Changes detected, auto-committing..."
            auto_commit_wip
            
            # 一定時間後に自動プッシュ
            if [ $(($(date +%s) % ($AUTO_COMMIT_INTERVAL * 60))) -eq 0 ]; then
                auto_push_safe
            fi
        fi
        
        # 定期バックアップ
        if [ $(($(date +%s) % ($BACKUP_INTERVAL * 60))) -eq 0 ]; then
            periodic_backup
        fi
        
        sleep 60
    done
}

# クリーンアップ自動化
automated_cleanup() {
    echo -e "${BLUE}🧹 Running automated cleanup...${NC}"
    
    # 古いバックアップブランチを削除
    echo "Cleaning up old backup branches..."
    git branch | grep -E "(backup|auto-backup)" | while read branch; do
        local branch_date=$(echo $branch | grep -o '[0-9]\{8\}')
        if [ -n "$branch_date" ]; then
            local days_old=$(( ($(date +%s) - $(date -d "$branch_date" +%s)) / 86400 ))
            if [ "$days_old" -gt 7 ]; then
                echo "Deleting old backup branch: $branch"
                git branch -D $branch
            fi
        fi
    done
    
    # 未使用のリモートブランチを削除
    echo "Cleaning up remote tracking branches..."
    git remote prune origin
    
    # ガベージコレクション
    echo "Running garbage collection..."
    git gc --aggressive --prune=now
    
    echo -e "${GREEN}✅ Automated cleanup completed${NC}"
}

# ヘルプ表示
show_automation_help() {
    echo -e "${BLUE}🤖 Git Automation Script${NC}"
    echo "==============================="
    echo "Available commands:"
    echo "  auto_commit_wip      - Auto-commit work in progress"
    echo "  auto_push_safe       - Safe auto-push with checks"
    echo "  periodic_backup      - Create periodic backup"
    echo "  health_check         - Run project health check"
    echo "  generate_report      - Generate Git statistics report"
    echo "  watch_mode           - Start continuous monitoring"
    echo "  automated_cleanup    - Run automated cleanup"
    echo ""
    echo "Automation features:"
    echo "  - Auto-commit WIP changes"
    echo "  - Safe auto-push with conflict detection"
    echo "  - Periodic backups"
    echo "  - Project health monitoring"
    echo "  - Automated cleanup"
    echo ""
}

# メイン処理
main() {
    if [ $# -eq 0 ]; then
        show_automation_help
        return 0
    fi
    
    case "$1" in
        "wip")
            auto_commit_wip
            ;;
        "push")
            auto_push_safe
            ;;
        "backup")
            periodic_backup
            ;;
        "health")
            health_check
            ;;
        "report")
            generate_report
            ;;
        "watch")
            watch_mode
            ;;
        "cleanup")
            automated_cleanup
            ;;
        "help"|"-h"|"--help")
            show_automation_help
            ;;
        *)
            echo -e "${RED}❌ Unknown command: $1${NC}"
            show_automation_help
            ;;
    esac
}

# スクリプトが直接実行された場合
if [[ "${BASH_SOURCE[0]}" == "${0}" ]]; then
    main "$@"
fi
