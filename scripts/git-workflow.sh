#!/bin/bash

# Git ワークフロー自動化スクリプト

# 設定
DEFAULT_BRANCH="main"
BACKUP_BRANCH="backup-$(date +%Y%m%d-%H%M%S)"

# 色定義
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

# 現在の作業を安全にバックアップ
backup_current_work() {
    echo -e "${BLUE}💾 Backing up current work...${NC}"
    
    # 現在のブランチ名を取得
    current_branch=$(git branch --show-current)
    
    # 変更があるかチェック
    if ! git diff-index --quiet HEAD --; then
        echo "Creating backup branch: $BACKUP_BRANCH"
        git checkout -b $BACKUP_BRANCH
        git add -A
        git commit -m "Backup: Work in progress on $current_branch"
        git checkout $current_branch
        echo -e "${GREEN}✅ Backup created: $BACKUP_BRANCH${NC}"
    else
        echo "No changes to backup."
    fi
}

# 機能ブランチの作成
create_feature_branch() {
    local feature_name="$1"
    if [ -z "$feature_name" ]; then
        echo -e "${RED}❌ Feature name required${NC}"
        echo "Usage: create_feature_branch \"feature-name\""
        return 1
    fi
    
    local branch_name="feature/$feature_name"
    
    echo -e "${BLUE}🌿 Creating feature branch: $branch_name${NC}"
    
    # メインブランチに切り替え
    git checkout $DEFAULT_BRANCH
    git pull origin $DEFAULT_BRANCH
    
    # 新しいブランチを作成
    git checkout -b $branch_name
    
    echo -e "${GREEN}✅ Feature branch created: $branch_name${NC}"
}

# 機能ブランチの完了
finish_feature_branch() {
    local current_branch=$(git branch --show-current)
    
    if [[ ! $current_branch =~ ^feature/ ]]; then
        echo -e "${RED}❌ Not on a feature branch${NC}"
        return 1
    fi
    
    echo -e "${BLUE}🏁 Finishing feature branch: $current_branch${NC}"
    
    # 最新の変更をコミット
    if ! git diff-index --quiet HEAD --; then
        echo "Committing current changes..."
        git add -A
        git commit -m "Complete feature: ${current_branch#feature/}"
    fi
    
    # メインブランチにマージ
    git checkout $DEFAULT_BRANCH
    git pull origin $DEFAULT_BRANCH
    git merge $current_branch
    
    # リモートにプッシュ
    git push origin $DEFAULT_BRANCH
    
    # 機能ブランチを削除
    git branch -d $current_branch
    
    echo -e "${GREEN}✅ Feature branch completed and merged${NC}"
}

# リリースの準備
prepare_release() {
    local version="$1"
    if [ -z "$version" ]; then
        echo -e "${RED}❌ Version number required${NC}"
        echo "Usage: prepare_release \"v1.0.0\""
        return 1
    fi
    
    echo -e "${BLUE}🚀 Preparing release: $version${NC}"
    
    # リリースブランチを作成
    local release_branch="release/$version"
    git checkout $DEFAULT_BRANCH
    git pull origin $DEFAULT_BRANCH
    git checkout -b $release_branch
    
    # バージョン情報を更新（package.jsonがある場合）
    if [ -f "package.json" ]; then
        npm version $version --no-git-tag-version
        git add package.json
        git commit -m "Bump version to $version"
    fi
    
    echo -e "${GREEN}✅ Release branch created: $release_branch${NC}"
    echo "Next steps:"
    echo "1. Test the release"
    echo "2. Run: finish_release $version"
}

# リリースの完了
finish_release() {
    local version="$1"
    if [ -z "$version" ]; then
        echo -e "${RED}❌ Version number required${NC}"
        return 1
    fi
    
    local release_branch="release/$version"
    
    echo -e "${BLUE}🏁 Finishing release: $version${NC}"
    
    # リリースブランチをメインにマージ
    git checkout $DEFAULT_BRANCH
    git merge $release_branch
    
    # タグを作成
    git tag -a $version -m "Release $version"
    
    # リモートにプッシュ
    git push origin $DEFAULT_BRANCH
    git push origin $version
    
    # リリースブランチを削除
    git branch -d $release_branch
    
    echo -e "${GREEN}✅ Release $version completed${NC}"
}

# ホットフィックスの作成
create_hotfix() {
    local hotfix_name="$1"
    if [ -z "$hotfix_name" ]; then
        echo -e "${RED}❌ Hotfix name required${NC}"
        return 1
    fi
    
    local hotfix_branch="hotfix/$hotfix_name"
    
    echo -e "${BLUE}🔥 Creating hotfix branch: $hotfix_branch${NC}"
    
    git checkout $DEFAULT_BRANCH
    git pull origin $DEFAULT_BRANCH
    git checkout -b $hotfix_branch
    
    echo -e "${GREEN}✅ Hotfix branch created: $hotfix_branch${NC}"
}

# 同期とクリーンアップ
sync_and_cleanup() {
    echo -e "${BLUE}🔄 Syncing and cleaning up...${NC}"
    
    # リモートの最新情報を取得
    git fetch origin --prune
    
    # マージ済みブランチを削除
    echo "Cleaning up merged branches..."
    git branch --merged | grep -v "\*\|$DEFAULT_BRANCH\|master\|develop" | xargs -n 1 git branch -d 2>/dev/null
    
    # ガベージコレクション
    git gc --prune=now
    
    echo -e "${GREEN}✅ Sync and cleanup completed${NC}"
}

# プロジェクトの初期化
init_project() {
    echo -e "${BLUE}🌟 Initializing project...${NC}"
    
    # .gitignoreの作成
    if [ ! -f ".gitignore" ]; then
        cat > .gitignore << EOF
# Dependencies
node_modules/
vendor/

# Build outputs
build/
dist/
public/build/

# Environment files
.env
.env.local
.env.production

# IDE
.vscode/
.idea/
*.swp
*.swo

# OS
.DS_Store
Thumbs.db

# Logs
*.log
logs/

# Temporary files
tmp/
temp/
EOF
        git add .gitignore
        git commit -m "Add .gitignore"
    fi
    
    # 初期コミット
    if [ -z "$(git log --oneline 2>/dev/null)" ]; then
        git add .
        git commit -m "Initial commit"
    fi
    
    echo -e "${GREEN}✅ Project initialized${NC}"
}

# ヘルプ表示
show_workflow_help() {
    echo -e "${BLUE}🔧 Git Workflow Script${NC}"
    echo "==============================="
    echo "Available commands:"
    echo "  backup_current_work          - Backup current work"
    echo "  create_feature_branch <name> - Create feature branch"
    echo "  finish_feature_branch        - Finish current feature"
    echo "  prepare_release <version>    - Prepare release"
    echo "  finish_release <version>     - Finish release"
    echo "  create_hotfix <name>         - Create hotfix branch"
    echo "  sync_and_cleanup             - Sync and cleanup"
    echo "  init_project                 - Initialize project"
    echo ""
    echo "Example workflow:"
    echo "  1. create_feature_branch \"login-system\""
    echo "  2. [work on feature]"
    echo "  3. finish_feature_branch"
    echo "  4. prepare_release \"v1.0.0\""
    echo "  5. finish_release \"v1.0.0\""
    echo ""
}

# メイン処理
main() {
    if [ $# -eq 0 ]; then
        show_workflow_help
        return 0
    fi
    
    case "$1" in
        "backup")
            backup_current_work
            ;;
        "feature")
            create_feature_branch "$2"
            ;;
        "finish-feature")
            finish_feature_branch
            ;;
        "prepare-release")
            prepare_release "$2"
            ;;
        "finish-release")
            finish_release "$2"
            ;;
        "hotfix")
            create_hotfix "$2"
            ;;
        "sync")
            sync_and_cleanup
            ;;
        "init")
            init_project
            ;;
        "help"|"-h"|"--help")
            show_workflow_help
            ;;
        *)
            echo -e "${RED}❌ Unknown command: $1${NC}"
            show_workflow_help
            ;;
    esac
}

# スクリプトが直接実行された場合
if [[ "${BASH_SOURCE[0]}" == "${0}" ]]; then
    main "$@"
fi
