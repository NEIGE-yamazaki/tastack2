#!/bin/bash

# Git統合エイリアス - 全てのGitスクリプトへの統一アクセス

# スクリプトのパス
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
HELPER_SCRIPT="$SCRIPT_DIR/git-helper.sh"
WORKFLOW_SCRIPT="$SCRIPT_DIR/git-workflow.sh"
AUTOMATION_SCRIPT="$SCRIPT_DIR/git-automation.sh"

# 色定義
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
PURPLE='\033[0;35m'
NC='\033[0m'

# 統合ヘルプ
show_integrated_help() {
    echo -e "${BLUE}🚀 Git Integration Script${NC}"
    echo "==============================="
    echo -e "${GREEN}Quick Commands:${NC}"
    echo "  st, status     - Git status overview"
    echo "  br, branches   - Branch information"
    echo "  lg, log        - Pretty git log"
    echo "  co <msg>       - Quick commit"
    echo "  ps, push       - Safe push"
    echo "  pl, pull       - Smart pull"
    echo "  sync           - Sync and cleanup"
    echo ""
    echo -e "${YELLOW}Workflow Commands:${NC}"
    echo "  feat <name>    - Create feature branch"
    echo "  finish         - Finish feature branch"
    echo "  hotfix <name>  - Create hotfix branch"
    echo "  release <ver>  - Prepare release"
    echo "  backup         - Backup current work"
    echo ""
    echo -e "${PURPLE}Automation Commands:${NC}"
    echo "  wip            - Auto-commit WIP"
    echo "  watch          - Start watch mode"
    echo "  health         - Health check"
    echo "  report         - Generate report"
    echo "  cleanup        - Automated cleanup"
    echo ""
    echo -e "${BLUE}Advanced:${NC}"
    echo "  helper         - Open git helper"
    echo "  workflow       - Open git workflow"
    echo "  automation     - Open git automation"
    echo ""
}

# メイン処理
main() {
    if [ $# -eq 0 ]; then
        show_integrated_help
        return 0
    fi
    
    case "$1" in
        # Quick commands
        "st"|"status")
            source $HELPER_SCRIPT
            git_status
            ;;
        "br"|"branches")
            source $HELPER_SCRIPT
            git_branches
            ;;
        "lg"|"log")
            source $HELPER_SCRIPT
            git_log_pretty
            ;;
        "co"|"commit")
            source $HELPER_SCRIPT
            git_quick_commit "$2"
            ;;
        "ps"|"push")
            source $HELPER_SCRIPT
            git_safe_push
            ;;
        "pl"|"pull")
            git pull --rebase
            ;;
        "sync")
            source $WORKFLOW_SCRIPT
            sync_and_cleanup
            ;;
        
        # Workflow commands
        "feat"|"feature")
            source $WORKFLOW_SCRIPT
            create_feature_branch "$2"
            ;;
        "finish")
            source $WORKFLOW_SCRIPT
            finish_feature_branch
            ;;
        "hotfix")
            source $WORKFLOW_SCRIPT
            create_hotfix "$2"
            ;;
        "release")
            source $WORKFLOW_SCRIPT
            prepare_release "$2"
            ;;
        "backup")
            source $WORKFLOW_SCRIPT
            backup_current_work
            ;;
        
        # Automation commands
        "wip")
            source $AUTOMATION_SCRIPT
            auto_commit_wip
            ;;
        "watch")
            source $AUTOMATION_SCRIPT
            watch_mode
            ;;
        "health")
            source $AUTOMATION_SCRIPT
            health_check
            ;;
        "report")
            source $AUTOMATION_SCRIPT
            generate_report
            ;;
        "cleanup")
            source $AUTOMATION_SCRIPT
            automated_cleanup
            ;;
        
        # Advanced
        "helper")
            shift
            bash $HELPER_SCRIPT "$@"
            ;;
        "workflow")
            shift
            bash $WORKFLOW_SCRIPT "$@"
            ;;
        "automation")
            shift
            bash $AUTOMATION_SCRIPT "$@"
            ;;
        
        "help"|"-h"|"--help")
            show_integrated_help
            ;;
        *)
            echo -e "${RED}❌ Unknown command: $1${NC}"
            show_integrated_help
            ;;
    esac
}

# エイリアス設定関数
setup_aliases() {
    echo -e "${BLUE}⚙️  Setting up Git aliases...${NC}"
    
    # Bashエイリアス
    local alias_file="$HOME/.git_aliases"
    cat > $alias_file << EOF
# Git Integration Aliases
alias g='$SCRIPT_DIR/git-integration.sh'
alias gst='$SCRIPT_DIR/git-integration.sh st'
alias gco='$SCRIPT_DIR/git-integration.sh co'
alias gps='$SCRIPT_DIR/git-integration.sh ps'
alias gpl='$SCRIPT_DIR/git-integration.sh pl'
alias gbr='$SCRIPT_DIR/git-integration.sh br'
alias glg='$SCRIPT_DIR/git-integration.sh lg'
alias gfeat='$SCRIPT_DIR/git-integration.sh feat'
alias ghotfix='$SCRIPT_DIR/git-integration.sh hotfix'
alias gwip='$SCRIPT_DIR/git-integration.sh wip'
alias gsync='$SCRIPT_DIR/git-integration.sh sync'
EOF
    
    echo -e "${GREEN}✅ Aliases created in $alias_file${NC}"
    echo "Add this to your ~/.bashrc:"
    echo "source $alias_file"
}

# 初期化
if [ "$1" = "setup" ]; then
    setup_aliases
    exit 0
fi

# メイン実行
main "$@"
